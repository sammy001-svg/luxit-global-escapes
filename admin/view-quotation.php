<?php
require_once '../includes/db.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) die("Quotation ID required");

$stmt = $pdo->prepare("SELECT q.*, c.name as customer_name, c.email as customer_email, c.country as customer_country 
                       FROM quotations q 
                       JOIN customers c ON q.customer_id = c.id 
                       WHERE q.id = ?");
$stmt->execute([$id]);
$quote = $stmt->fetch();

if (!$quote) die("Quotation not found");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quotation - <?php echo $quote['id']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; color: black; }
            .glass { border: none; box-shadow: none; background: white; }
        }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 p-4 md:p-12">

    <div class="max-w-4xl mx-auto bg-white shadow-2xl rounded-2xl overflow-hidden border border-slate-200 glass">
        <!-- Toolbar -->
        <div class="no-print bg-slate-900 text-white p-4 flex justify-between items-center">
            <a href="index.php" class="text-sm hover:text-primary transition"><i class="fas fa-arrow-left mr-2"></i> Back to Dashboard</a>
            <button onclick="window.print()" class="bg-primary px-6 py-2 rounded-xl text-sm font-bold hover:bg-opacity-90 transition">
                <i class="fas fa-download mr-2"></i> Download PDF
            </button>
        </div>

        <!-- Quotation Content -->
        <div class="p-8 md:p-16">
            <div class="flex flex-col md:flex-row justify-between items-start mb-12">
                <div>
                    <img src="../assets/images/luxit-africa-logo.png" alt="Luxit Logo" class="w-40 mb-4">
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Luxit Global Escapes Ltd.<br>
                        123 Luxury Avenue, Westlands<br>
                        Nairobi, Kenya<br>
                        +254 700 000 000 | info@luxit.com
                    </p>
                </div>
                <div class="mt-8 md:mt-0 text-right">
                    <h1 class="text-4xl font-black text-slate-900 uppercase tracking-tighter">Quotation</h1>
                    <p class="text-slate-500 font-bold mt-2">#<?php echo $quote['id']; ?></p>
                    <div class="mt-4 space-y-1">
                        <p class="text-xs text-slate-400 uppercase font-bold">Offer Date</p>
                        <p class="text-sm font-bold"><?php echo date('M d, Y', strtotime($quote['created_at'])); ?></p>
                        <p class="text-xs text-slate-400 uppercase font-bold mt-2">Valid Until</p>
                        <p class="text-sm font-bold text-amber-500"><?php echo date('M d, Y', strtotime($quote['valid_until'])); ?></p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-12 mb-12 border-y border-slate-100 py-8">
                <div>
                    <p class="text-xs text-slate-400 uppercase font-bold mb-4">Prepared For:</p>
                    <h3 class="text-xl font-bold text-slate-900"><?php echo $quote['customer_name']; ?></h3>
                    <p class="text-slate-500 text-sm mt-1"><?php echo $quote['customer_email']; ?></p>
                    <p class="text-slate-500 text-sm"><?php echo $quote['customer_country']; ?></p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-400 uppercase font-bold mb-4">Status:</p>
                    <span class="inline-block px-4 py-1.5 rounded-full text-xs font-black uppercase bg-blue-100 text-blue-600">
                        <?php echo $quote['status']; ?>
                    </span>
                </div>
            </div>

            <div class="mb-12 p-6 bg-slate-50 rounded-2xl border border-slate-100">
                <h4 class="text-xs text-slate-400 uppercase font-bold mb-4">Proposal Summary:</h4>
                <p class="text-slate-900 font-medium leading-relaxed">
                    We are pleased to offer you the <span class="text-primary font-bold">"<?php echo $quote['tour_name']; ?>"</span> travel package. This bespoke experience has been curated specifically to match your preferences and travel style.
                </p>
            </div>

            <table class="w-full mb-12">
                <thead>
                    <tr class="text-left border-b-2 border-slate-900">
                        <th class="py-4 text-xs font-black uppercase tracking-widest text-slate-500">Service / Package</th>
                        <th class="py-4 text-right text-xs font-black uppercase tracking-widest text-slate-500">Estimated Cost</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="py-6">
                            <p class="font-bold text-slate-900"><?php echo $quote['tour_name']; ?></p>
                            <p class="text-sm text-slate-500 mt-1 italic">Includes accommodation, transfers, and guided excursions.</p>
                        </td>
                        <td class="py-6 text-right font-bold text-slate-900">$<?php echo number_format($quote['amount'], 2); ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="flex justify-end">
                <div class="w-full md:w-64 space-y-3">
                    <div class="flex justify-between text-2xl font-black text-slate-900 pt-3 border-t-2 border-slate-900">
                        <span>Quote Total</span>
                        <span>$<?php echo number_format($quote['amount'], 2); ?></span>
                    </div>
                </div>
            </div>

            <div class="mt-24 p-8 bg-slate-900 text-white rounded-2xl">
                <h4 class="font-bold mb-2">Terms & Conditions</h4>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Prices are subject to availability at the time of booking. This quotation is valid until <?php echo date('M d, Y', strtotime($quote['valid_until'])); ?>. Full payment or a deposit is required to secure the reservation.
                </p>
            </div>
        </div>
    </div>

</body>
</html>
