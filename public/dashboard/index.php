<?php
require_once __DIR__ . '/../../config/db.php';


// Fetch data using PDO

$idParam = $_GET['id'] ?? 1;
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $idParam]);
$user = $stmt->fetch();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelakom FM - Dashboard</title>
    <link rel="stylesheet" href="/css/style.css">
</head>

<body class="bg-[#F9FAFB] text-gray-100 flex overflow-hidden h-screen">
    <aside class="w-64 bg-white border-r border-r-gray-200 flex flex-col p-8 gap-3">
        <div class="h-24 w-full bg-[url('/assets/brand.png')] bg-contain bg-center bg-no-repeat"></div>
        <p class="text-gray-400 font-normal text-xs">Menu</p>
        <section class="h-fit w-full flex flex-row text-gray-700 items-center gap-4 rounded-sm px-2 py-1 transition-colors duration-20 hover:bg-[#EFEFEF]">
            <span class="material-symbols-outlined text-xl">space_dashboard</span>
            <p class="font-medium text-sm">Dashboard</p>
        </section>
        <section class="h-fit w-full flex flex-row text-gray-700 items-center gap-4 rounded-sm px-2 py-1 transition-colors duration-20 hover:bg-[#EFEFEF]">
            <span class="material-symbols-outlined text-xl">package_2</span>
            <p class="font-medium text-sm">Item Loan</p>
        </section>
        <section class="h-fit w-full flex flex-row text-gray-700 items-center gap-4 rounded-sm px-2 py-1 transition-colors duration-20 hover:bg-[#EFEFEF]">
            <span class="material-symbols-outlined text-xl">meeting_room</span>
            <p class="font-medium text-sm">Room Reservation</p>
        </section>
        <section class="h-fit w-full flex flex-row text-gray-700 items-center gap-4 rounded-sm px-2 py-1 transition-colors duration-20 hover:bg-[#EFEFEF]">
            <span class="material-symbols-outlined text-xl">package_2</span>
            <p class="font-medium text-sm">Complain</p>
        </section>
        <div class="px-2 h-fit">
            <div class="border-b border-gray-300"></div>
        </div>
        <section class="h-fit w-full flex flex-row text-gray-700 items-center gap-4 rounded-sm px-2 py-1 transition-colors duration-20 hover:bg-[#EFEFEF]">
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
                    <h1 class="text-2xl text-gray-900 font-semibold">Welcome, <?php echo $user['name']; ?></h1>
                    <p class="text-[0.9rem]">Manage and request your reservations.</p>
                </div>
                <div class="flex flex-row p-2 h-full gap-4">
                    <div class="rounded-full border-gray-400 border h-full aspect-square bg-[url('/assets/profile.jpeg')] bg-contain bg-center bg-no-repeat overflow-hidden"></div>
                    <div class="flex flex-col text-[0.75rem] justify-center">
                        <p class="font-semibold"><?php echo explode(' ', $user['name'])[0]; ?></p>
                        <p><?php echo $user['email']; ?></p>
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
                            <p class="text-xl font-bold">3</p>
                        </div>
                        <span class="material-symbols-outlined text-5xl text-[#DC6803]">package_2</span>
                    </section>
                    <section class="rounded-lg h-fit flex-1 border-gray-100 border bg-white flex flex-row justify-between px-6 items-center py-8 transition-colors duration-100 hover:bg-[#EFEFEF]">
                        <div class="flex flex-col text-[#101828] gap-4">
                            <label for="" class="text-sm font-semibold">Approved Reservations</label>
                            <p class="text-xl font-bold">4</p>
                        </div>
                        <span class="material-symbols-outlined text-5xl text-[#039855]">package_2</span>
                    </section>
                    <section class="rounded-lg h-fit flex-1 border-gray-100 border bg-white flex flex-row justify-between px-6 items-center py-8 transition-colors duration-100 hover:bg-[#EFEFEF]">
                        <div class="flex flex-col text-[#101828] gap-4">
                            <label for="" class="text-sm font-semibold">Rejected Reservations</label>
                            <p class="text-xl font-bold">3</p>
                        </div>
                        <span class="material-symbols-outlined text-5xl text-[#EF3330]">package_2</span>
                    </section>
                </div>
                <div class="flex flex-row h-fit gap-4 shrink-0">
                    <section class="border-gray-100 border flex-row flex rounded-lg h-fit flex-1 bg-white justify-between px-6 py-8 items-center transition-colors duration-100 hover:bg-[#EFEFEF]">
                        <div class="flex flex-col gap-4 text-[#101828]">
                            <label for="" class="text-sm font-semibold">Total complains</label>
                            <p class="font-bold text-xl">1</p>
                        </div>
                        <span class="material-symbols-outlined text-5xl text-[#7F56D9]">note_stack</span>
                    </section>
                    <section class="border-gray-100 border flex-row flex rounded-lg h-fit flex-1 bg-white justify-between px-6 py-8 items-center transition-colors duration-100 hover:bg-[#EFEFEF]">
                        <div class="flex flex-col gap-4 text-[#101828]">
                            <label for="" class="text-sm font-semibold">Total Reservations</label>
                            <p class="font-bold text-xl">10</p>
                        </div>
                        <span class="material-symbols-outlined text-5xl text-[#3C98EE]">package_2</span>
                    </section>
                </div>

                <!-- 4. CHANGED h-full to flex-1 min-h-0 HERE -->
                <div class="flex-1 min-h-0 w-full flex flex-col bg-white border rounded-lg border-gray-100">
                    <div class="flex flex-row w-full justify-between shrink-0">
                        <div class="flex px-4 py-4 flex-col gap-2 text-[#101828]">
                            <label for="" class="text-sm font-semibold">Recent Reservations</label>
                            <p class="font-medium text-xs text-gray-500">Your reserved requests today</p>
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
                    <div class="flex-1 w-full p-4 overflow-y-auto">

                        <!-- List Items... -->
                        <div class="w-full h-fit flex flex-row justify-between p-2 transition-colors duration-20 hover:bg-[#EFEFEF] rounded-lg bg-[#fafafafa] mb-2">
                            <p class="font-light text-sm text-gray-500">Item</p>
                            <p class="font-bold text-sm text-[#12B76A]">Approved</p>
                        </div>
                        <div class="w-full h-fit flex flex-row justify-between p-2 transition-colors duration-20 hover:bg-[#EFEFEF] rounded-lg bg-[#fafafafa] mb-2">
                            <p class="font-light text-sm text-gray-500">Item</p>
                            <p class="font-bold text-sm text-[#FE9F43]">Pending</p>
                        </div>
                        <div class="w-full h-fit flex flex-row justify-between p-2 transition-colors duration-20 hover:bg-[#EFEFEF] rounded-lg bg-[#fafafafa] mb-2">
                            <p class="font-light text-sm text-gray-500">Item</p>
                            <p class="font-bold text-sm text-[#F44646]">Rejected</p>
                        </div>


                    </div>
                </div>
            </div>
        </main>
        <footer class="text-gray-800 shrink-0 mt-4 rounded-xl">
            <p class="text-gray-400">&copy; 2026 <b class="text-[#0D5ACC]">Pelakom</b> All rights reserved</p>
        </footer>
    </div>

</body>

</html>