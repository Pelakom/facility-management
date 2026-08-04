<?php
require_once __DIR__ . '/../../config/db.php';
// Fetch data using PDO


// Fetch data using PDO
$useridParam = $_GET['userid'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Define the required fields from your form
        $requiredFields = ['selected-item-id', 'startdate', 'enddate', 'amount', 'purpose'];

        foreach ($requiredFields as $field) {
            // Check if the key does not exist or contains an empty string/null
            if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
                throw new InvalidArgumentException("Missing or empty required field: " . $field);
            }
        }

        // Fix 1: Use intval() for Foreign Keys to guarantee an integer is sent to MySQL
        $itemId    = intval($_POST['selected-item-id']);
        $amount    = intval($_POST['amount']);

        // Fix 2: Removed the double dollar sign ($$) typo
        $useridParam = intval($_POST['userid'] ?? $useridParam);

        $startDate = htmlspecialchars($_POST['startdate']);
        $endDate   = htmlspecialchars($_POST['enddate']);
        $purpose   = htmlspecialchars($_POST['purpose']);

        // Check if this is an UPDATE or an INSERT
        $isUpdate = !empty($_POST['selected-list-id']) && trim($_POST['selected-list-id']) !== '';

        if (!$isUpdate) {
            $pdoQuery = "INSERT INTO lists (item_id, user_id, total_amount, purpose, duration) 
                         VALUES (:itemId, :userId, :totalAmount, :purpose, :duration)";
        } else {
            $pdoQuery = "UPDATE lists SET item_id = :itemId, total_amount = :totalAmount, 
                         purpose = :purpose, duration = :duration 
                         WHERE id = :selectedListId AND user_id = :userId";
        }

        // print_r($_POST);
        // echo $pdoQuery;
        // exit;

        $stmt = $pdo->prepare($pdoQuery);

        // Build the base parameters
        $params = [
            ':itemId'      => $itemId,
            ':userId'      => $useridParam,
            ':totalAmount' => $amount,
            ':purpose'     => $purpose,
            ':duration'    => ($startDate . ' - ' . $endDate)
        ];

        // Fix 3: Add the missing colon to the selectedListId parameter
        if ($isUpdate) {
            $params[':selectedListId'] = intval($_POST['selected-list-id']);
        }

        $stmt->execute($params);
    } catch (InvalidArgumentException $e) {
        http_response_code(400); // Bad Request
        echo "Validation Error: " . $e->getMessage();
        exit;
    } catch (PDOException $e) {
        // Catch Database-specific errors separately to debug easily
        http_response_code(500);
        echo "Database Error: " . $e->getMessage();
        // If you see this, $itemId is definitely passing a number that isn't in the items table
        exit;
    } catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        echo "An unexpected error occurred: " . $e->getMessage();
        exit;
    }
}


$stmt = $pdo->prepare("SELECT lists.id AS list_id,lists.user_id,items.name,lists.item_id,lists.total_amount,lists.purpose,lists.duration,lists.status FROM lists INNER JOIN items ON lists.item_id = items.id WHERE lists.user_id = :id ORDER BY created_at DESC");
$stmt->execute(['id' => $useridParam]);
$lists = $stmt->fetchAll();


$itemsStmt = $pdo->prepare("SELECT id, name, description FROM items");
$itemsStmt->execute();
$items = $itemsStmt->fetchAll();

$totalList = 0;
$totalPendingList = 0;
$totalRejectedList = 0;
$totalApprovedList = 0;

foreach ($lists as $list) {
    $totalList++;
    $status = $list['status'];
    if ($status == 'pending') {
        $totalPendingList++;
    } elseif ($status == 'rejected') {
        $totalRejectedList++;
    } elseif ($status == 'approved') {
        $totalApprovedList++;
    }
}


?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelakom FM - Dashboard</title>
    <link rel="stylesheet" href="/css/style.css">
    <script src="/assets/dashboard.js"></script>
</head>

<body class="bg-[#F9FAFB] text-gray-100 flex overflow-hidden h-screen">

    <dialog id="request-modal"
        class="m-auto w-full max-w-3xl max-h-[90vh] overflow-visible  shadow-2xl border-none backdrop:bg-black/40 backdrop:backdrop-blur-xs rounded-xl focus:outline-none">

        <div class="flex flex-col rounded-xl bg-white p-8 focus:outline-none" id="main-dialog-box">
            <div class="flex flex-col gap-5 focus:outline-none" id="create-request-box">
                <div class="text-center">
                    <h2 autofocus tabindex="-10000" class="text-xl font-bold text-gray-900 focus:outline-none" id="box-title">Add A New Request</h2>
                    <p class="text-sm text-gray-500 mt-1" id="box-description">Reserve a request for loan.</p>
                </div>

                <form method="POST" action="" class="flex flex-col gap-4 mt-2">

                    <div class="flex flex-col w-full h-fit gap-3">
                        <div class="relative w-full">
                            <div class="grid grid-cols-[auto_1fr] w-full focus-within:ring-2 focus-within:ring-blue-500 rounded-xl">
                                <div class="flex h-full aspect-square items-center justify-center bg-gray-100 text-[#204063] rounded-l-xl">
                                    <span class="material-symbols-outlined text-2xl">search</span>
                                </div>
                                <input id="search-input" type="text" placeholder="Search for an item..." name="search"
                                    class="w-full bg-gray-100 px-0 py-3 text-sm text-[#204063] focus:outline-none text-left rounded-r-xl"
                                    onfocus="forceDropdownOpen(true)" oninput="filterOptions()" />
                            </div>

                            <div id="dropdown-menu"
                                class="absolute hidden top-full left-0 mt-2 w-full max-h-64 bg-gray-200 z-50 rounded-sm shadow-lg overflow-y-auto overflow-hidden custom-scrollbar space-y-0.5">
                                <?php foreach ($items as $item): ?>
                                    <section
                                        class="option-item w-full h-fit flex flex-row justify-between p-2 transition-colors duration-20 bg-[#fafafafa] hover:bg-[#EFEFEF] items-center cursor-pointer"
                                        onclick="selectOption('<?php echo $item['id']; ?>', '<?php echo htmlspecialchars($item['name']); ?>');">
                                        <div class="h-fit w-full flex flex-col gap-1">
                                            <p class="font-semibold text-sm text-gray-700"><?php echo htmlspecialchars($item['name']); ?></p>
                                            <p class="font-light text-xs text-gray-500"><?php echo htmlspecialchars($item['description']); ?></p>
                                        </div>
                                    </section>
                                <?php endforeach; ?>
                                <section id="no-results"
                                    class="hidden optiondocument.addEventListener()-item w-full h-fit flex flex-row justify-between p-2 transition-colors duration-20 bg-[#fafafafa] hover:bg-[#EFEFEF] items-center cursor-not-allowed">
                                    <div class="h-fit w-full flex flex-col gap-1">
                                        <p class="font-medium text-sm text-gray-700">No Results</p>
                                    </div>
                                </section>
                            </div>
                        </div>

                        <!-- Hidden input & display label -->
                        <input type="hidden" name="selected-list-id" id="selected-list-id">
                        <input type="hidden" name="selected-item-id" id="selected-item-id">
                        <div id="selected-label" class="bg-[#E7E7E7] p-4 py-2 rounded-xl text-gray-500 select-none">
                            No Selected Item
                        </div>
                    </div>

                    <!-- Inputs row -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 w-full">

                        <!-- 1. Start Date -->
                        <div class="grid grid-cols-[auto_1fr] w-full rounded-xl bg-gray-100 overflow-hidden focus-within:ring-2 focus-within:ring-blue-500">
                            <div class="flex h-full aspect-square items-center justify-center text-[#204063] pl-3">
                                <span class="material-symbols-outlined text-xl">calendar_month</span>
                            </div>
                            <input onfocus="(this.type='datetime-local', this.showPicker?.())"
                                onblur="if(!this.value) this.type='text'" type="text" placeholder="Start Date" name="startdate" id="startdate"
                                class="w-full bg-transparent px-3 py-3 text-sm text-[#204063] focus:outline-none" />
                        </div>

                        <!-- 2. End Date -->
                        <div class="grid grid-cols-[auto_1fr] w-full rounded-xl bg-gray-100 overflow-hidden focus-within:ring-2 focus-within:ring-blue-500">
                            <div class="flex h-full aspect-square items-center justify-center text-[#204063] pl-3">
                                <span class="material-symbols-outlined text-xl">event</span>
                            </div>
                            <input onfocus="(this.type='datetime-local', this.showPicker?.())"
                                onblur="if(!this.value) this.type='text'" type="text" name="enddate" id="enddate" placeholder="End Date"
                                class="w-full bg-transparent px-3 py-3 text-sm text-[#204063] focus:outline-none" />
                        </div>

                        <!-- 3. Amount Field -->
                        <div class="grid grid-cols-[auto_1fr] w-full rounded-xl bg-gray-100 overflow-hidden focus-within:ring-2 focus-within:ring-blue-500">
                            <div class="flex h-full aspect-square items-center justify-center text-[#204063] pl-3">
                                <span class="material-symbols-outlined text-xl">add_circle</span>
                            </div>
                            <input type="number" placeholder="Enter amount" name="amount" id="amount" min="1" max="100"
                                class="w-full bg-gray-100 px-3 py-3 text-sm text-[#204063] focus:outline-none" />
                        </div>

                    </div>

                    <!-- Fixed duplicate id="purpose" -->
                    <textarea id="purpose" name="purpose" placeholder="Purpose"
                        class="w-full min-h-56 bg-gray-200 p-2 px-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>

                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <button type="button" onclick="document.getElementById('request-modal').close()"
                            class="w-full rounded-xl border cursor-pointer border-gray-300 py-3 text-sm font-semibold text-gray-700 hover:text-white hover:bg-red-600">
                            Cancel
                        </button>
                        <button type="submit" id="submit-btn"
                            class="w-full rounded-xl bg-gray-200 py-3 cursor-not-allowed text-sm font-semibold text-gray-400" disabled>
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="flex flex-col rounded-xl bg-white p-8 focus:outline-none hidden" id="cancel-box"></div>

    </dialog>



    <aside class="w-64 bg-white border-r border-r-gray-200 flex flex-col p-8 gap-3">
        <div class="h-24 w-full bg-[url('/assets/brand.png')] bg-contain bg-center bg-no-repeat"></div>
        <p class="text-gray-400 font-normal text-xs">Menu</p>
        <section class="cursor-pointer h-fit w-full flex flex-row text-gray-700 items-center gap-4 rounded-sm px-2 py-1 hover:bg-[#EFEFEF]" onclick="window.location.replace('/dashboard/')">
            <span class="material-symbols-outlined text-xl">space_dashboard</span>
            <p class="font-medium text-sm">Dashboard</p>
        </section>
        <section class="cursor-pointer h-fit w-full flex flex-row text-white items-center gap-4 rounded-sm px-2 py-1 bg-[#0D5ACC] hover:bg-[#164893]" onclick="window.location.replace('/dashboard/items.php')">
            <span class="material-symbols-outlined text-xl">package_2</span>
            <p class="font-medium text-sm">Item Loan</p>
        </section>
        <section class="cursor-pointer h-fit w-full flex flex-row text-gray-700 items-center gap-4 rounded-sm px-2 py-1 hover:bg-[#EFEFEF]">
            <span class="material-symbols-outlined text-xl">meeting_room</span>
            <p class="font-medium text-sm">Room Reservation</p>
        </section>
        <section class="cursor-pointer h-fit w-full flex flex-row text-gray-700 items-center gap-4 rounded-sm px-2 py-1 hover:bg-[#EFEFEF]">
            <span class="material-symbols-outlined text-xl">package_2</span>
            <p class="font-medium text-sm">Complain</p>
        </section>
        <div class="px-2 h-fit">
            <div class="border-b border-gray-300"></div>
        </div>
        <section class="cursor-pointer h-fit w-full flex flex-row text-gray-700 items-center gap-4 rounded-sm px-2 py-1 hover:bg-[#EFEFEF]">
            <span class="material-symbols-outlined text-xl">history</span>
            <p class="font-medium text-sm">History</p>
        </section>

    </aside>
    <!-- 1. ADDED min-h-0 HERE -->
    <div class="flex flex-1 flex-col justify-between gap-4 p-8 min-h-0">

        <!-- 2. ADDED min-h-0 HERE -->
        <main class="flex-1 flex flex-col gap-4 min-h-0">
            <header class="bg-white border-gray-200 rounded-2xl items-center border flex flex-row gap-2 py-4 px-6 text-gray-500 shrink-0">
                <div class="flex-1 flex flex-col">
                    <h1 class="text-2xl text-gray-900 font-semibold">Welcome, name</h1>
                    <p class="text-[0.9rem]">Manage and request your reservations.</p>
                </div>
                <div class="flex flex-row p-2 h-full gap-4">
                    <div class="rounded-full border-gray-400 border h-full aspect-square bg-[url('/assets/profile.jpeg')] bg-contain bg-center bg-no-repeat overflow-hidden"></div>
                    <div class="flex flex-col text-[0.75rem] justify-center">
                        <p class="font-semibold">bombotron</p>
                        <p>email</p>
                    </div>
                </div>
                <div class="flex p-2 h-full items-center">
                    <div class="h-full justify-center items-center flex aspect-square rounded-xl hover:bg-[#FFE0E0] text-gray-600 transition-colors duration-100 hover:text-[#F13F3F]">
                        <span class="material-symbols-outlined text-xl">
                            logout
                        </span>
                    </div>
                </div>
            </header>

            <p class="font-normal text-[#667085] shrink-0">Dashboard</p>

            <!-- 3. ADDED min-h-0 HERE -->
            <div class="flex-1 flex flex-col w-auto gap-3 min-h-0">
                <div class="flex flex-row h-fit gap-4 shrink-0">
                    <section class="rounded-lg h-fit flex-1 border-gray-100 border bg-white flex flex-row justify-between px-6 items-center py-8 transition-colors duration-100 hover:bg-[#EFEFEF]">
                        <div class="flex flex-col text-[#101828] gap-4">
                            <label for="" class="text-sm font-semibold">Pending Reservations</label>
                            <p class="text-xl font-bold"><?php echo $totalPendingList ?></p>
                        </div>
                        <span class="material-symbols-outlined text-5xl text-[#DC6803]">package_2</span>
                    </section>
                    <section class="rounded-lg h-fit flex-1 border-gray-100 border bg-white flex flex-row justify-between px-6 items-center py-8 transition-colors duration-100 hover:bg-[#EFEFEF]">
                        <div class="flex flex-col text-[#101828] gap-4">
                            <label for="" class="text-sm font-semibold">Approved Reservations</label>
                            <p class="text-xl font-bold"><?php echo $totalApprovedList ?></p>
                        </div>
                        <span class="material-symbols-outlined text-5xl text-[#039855]">package_2</span>
                    </section>
                    <section class="rounded-lg h-fit flex-1 border-gray-100 border bg-white flex flex-row justify-between px-6 items-center py-8 transition-colors duration-100 hover:bg-[#EFEFEF]">
                        <div class="flex flex-col text-[#101828] gap-4">
                            <label for="" class="text-sm font-semibold">Rejected Reservations</label>
                            <p class="text-xl font-bold"><?php echo $totalRejectedList ?></p>
                        </div>
                        <span class="material-symbols-outlined text-5xl text-[#EF3330]">package_2</span>
                    </section>
                </div>
                <div class="flex flex-row h-fit gap-4 shrink-0">
                    <section class="border-gray-100 border flex-row flex rounded-lg h-fit flex-1 bg-white justify-between px-6 py-8 items-center transition-colors duration-100 hover:bg-[#EFEFEF]">
                        <div class="flex flex-col gap-4 text-[#101828]">
                            <label for="" class="text-sm font-semibold">Total Reservations</label>
                            <p class="font-bold text-xl"><?php echo $totalList ?></p>
                        </div>
                        <span class="material-symbols-outlined text-5xl text-[#3C98EE]">package_2</span>
                    </section>
                    <section class="border-gray-100 border flex-row flex rounded-lg h-full w-fit cursor-pointer bg-[#0D5ACC] justify-between gap-8 px-6 py-8 items-center transition-colors duration-100 hover:bg-[#144ca1]" onclick="createRequest()">
                        <div class="flex flex-col gap-4 text-white cursor-pointer">
                            <label for="" class="text-sm font-semibold cursor-pointer">Create New Request</label>
                        </div>
                        <span class="material-symbols-outlined text-5xl text-white">shadow_add</span>
                    </section>
                </div>

                <!-- 4. CHANGED h-full to flex-1 min-h-0 HERE -->
                <div class="flex-1 min-h-0 w-full flex flex-col bg-white border rounded-lg border-gray-100">
                    <div class="flex flex-row w-full justify-between shrink-0">
                        <div class="flex px-4 py-4 flex-col gap-2 text-[#101828]">
                            <label for="" class="text-sm font-semibold">Item Loans History</label>
                            <p class="font-medium text-xs text-gray-500">All your items reservation requests.</p>
                        </div>
                        <div class="h-full flex flex-row p-4">
                            <div class="flex justify-center items-center text-gray-400 h-10 aspect-square transition-colors duration-100 hover:bg-gray-200 rounded-xl">
                                <span class="material-symbols-outlined text-2xl">arrow_right_alt</span>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 w-full shrink-0">
                        <div class="border-b border-gray-200 w-full"></div>
                    </div>

                    <!-- 5. Left flex-1 and overflow-y-auto here to do the actual scrolling -->
                    <div class="px-6 pt-4 flex-row flex justify-between">
                        <p class="font-bold text-sm text-blue-950">Name</p>
                        <p class="font-bold text-sm text-blue-950">Status</p>
                    </div>
                    <div class="flex-1 w-full p-4 pt-2 overflow-y-auto flex flex-col gap-2">

                        <!-- List Items... -->
                        <?php foreach ($lists as $list): ?>
                            <section class="w-full h-fit flex flex-row justify-between p-2 transition-colors duration-20 rounded-lg bg-[#fafafafa] hover:bg-[#EFEFEF] items-center">
                                <div class="h-fit w-full flex flex-col gap-1">
                                    <p class="font-semibold text-sm text-gray-700"><?php echo htmlspecialchars($list['name'] . ' | ' . (int)$list['total_amount']); ?>x</p>
                                    <p class="font-light text-xs text-gray-500"><?php echo htmlspecialchars($list['duration'] . ' | ' . $list['purpose']); ?></p>
                                </div>
                                <?php if ($list['status'] == 'pending'): ?>
                                    <div class="h-full flex flex-row items-center w-fit gap-2">
                                        <span class="font-semibold material-symbols-outlined text-xl cursor-pointer text-gray-700 hover:text-[#0D5ACC]" onclick="editList(<?php echo $list['list_id']; ?>,<?php echo $list['item_id']; ?> , '<?php echo htmlspecialchars($list['name']); ?>', '<?php echo htmlspecialchars($list['duration']); ?>', '<?php echo htmlspecialchars($list['purpose']); ?>', '<?php echo htmlspecialchars((int)$list['total_amount']); ?>')" title="Edit">edit</span>
                                        <div class="h-full w-fit flex flex-col py-2">
                                            <div class="border-r-2 border-gray-300 flex-1"></div>
                                        </div>
                                        <span class="font-semibold material-symbols-outlined text-xl cursor-pointer text-gray-700 hover:text-[#0D5ACC]" onclick="editList(<?php echo $list['list_id']; ?>,<?php echo $list['item_id']; ?> , '<?php echo htmlspecialchars($list['name']); ?>', '<?php echo htmlspecialchars($list['duration']); ?>', '<?php echo htmlspecialchars($list['purpose']); ?>', '<?php echo htmlspecialchars((int)$list['total_amount']); ?>')" title="Edit">edit</span>
                                        <div class="h-full w-fit flex flex-col py-2">
                                            <div class="border-r-2 border-gray-300 flex-1"></div>
                                        </div>
                                        <p class="font-bold text-sm text-[#FE9F43]">Pending</p>
                                    </div>
                                <?php elseif ($list['status'] == 'approved'): ?>
                                    <p class="font-bold text-sm text-[#12B76A]">Approved</p>
                                <?php elseif ($list['status'] == 'rejected'): ?>
                                    <p class="font-bold text-sm text-[#F44646]">Rejected</p>
                                <?php endif; ?>
                            </section>
                        <?php endforeach; ?>

                    </div>
                </div>
            </div>
        </main>
        <footer class="text-gray-800 shrink-0 mt-4 rounded-xl">
            <p class="text-gray-400">&copy; 2026 <b class="text-[#0D5ACC]">Pelakom</b> All rights reserved</p>
        </footer>
    </div>

</body>

<noscript>
    <?php print_r($_POST ?? []) ?>
</noscript>

</html>