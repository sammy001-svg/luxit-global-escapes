<?php
require_once '../includes/db.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) die("Invoice ID required");

$stmt = $pdo->prepare("SELECT i.*, c.name as customer_name, c.email as customer_email, c.country as customer_country 
                       FROM invoices i 
                       JOIN customers c ON i.customer_id = c.id 
                       WHERE i.id = ?");
$stmt->execute([$id]);
$invoice = $stmt->fetch();

if (!$invoice) die("Invoice not found");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - <?php echo $invoice['id']; ?></title>
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

        <!-- Invoice Content -->
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
                    <h1 class="text-4xl font-black text-slate-900 uppercase tracking-tighter">Invoice</h1>
                    <p class="text-slate-500 font-bold mt-2">#<?php echo $invoice['id']; ?></p>
                    <div class="mt-4 space-y-1">
                        <p class="text-xs text-slate-400 uppercase font-bold">Issue Date</p>
                        <p class="text-sm font-bold"><?php echo date('M d, Y', strtotime($invoice['created_at'])); ?></p>
                        <p class="text-xs text-slate-400 uppercase font-bold mt-2">Due Date</p>
                        <p class="text-sm font-bold text-rose-500"><?php echo date('M d, Y', strtotime($invoice['due_date'])); ?></p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-12 mb-12 border-y border-slate-100 py-8">
                <div>
                    <p class="text-xs text-slate-400 uppercase font-bold mb-4">Bill To:</p>
                    <h3 class="text-xl font-bold text-slate-900"><?php echo $invoice['customer_name']; ?></h3>
                    <p class="text-slate-500 text-sm mt-1"><?php echo $invoice['customer_email']; ?></p>
                    <p class="text-slate-500 text-sm"><?php echo $invoice['customer_country']; ?></p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-400 uppercase font-bold mb-4">Payment Status:</p>
                    <span class="inline-block px-4 py-1.5 rounded-full text-xs font-black uppercase <?php 
                        echo $invoice['status'] === 'Paid' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600'; 
                    ?>">
                        <?php echo $invoice['status']; ?>
                    </span>
                </div>
            </div>

            <table class="w-full mb-12">
                <thead>
                    <tr class="text-left border-b-2 border-slate-900">
                        <th class="py-4 text-xs font-black uppercase tracking-widest text-slate-500">Description</th>
                        <th class="py-4 text-right text-xs font-black uppercase tracking-widest text-slate-500">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="py-6">
                            <p class="font-bold text-slate-900">Travel Package / Booking Reference</p>
                            <p class="text-sm text-slate-500 mt-1">Booking ID: <?php echo $invoice['booking_id'] ?? 'N/A'; ?></p>
                        </td>
                        <td class="py-6 text-right font-bold text-slate-900">$<?php echo number_format($invoice['amount'], 2); ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="flex justify-end">
                <div class="w-full md:w-64 space-y-3">
                    <div class="flex justify-between text-slate-500">
                        <span>Subtotal</span>
                        <span>$<?php echo number_format($invoice['amount'], 2); ?></span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Tax (0%)</span>
                        <span>$0.00</span>
                    </div>
                    <div class="flex justify-between text-2xl font-black text-slate-900 pt-3 border-t border-slate-900">
                        <span>Total</span>
                        <span>$<?php echo number_format($invoice['amount'], 2); ?></span>
                    </div>
                </div>
            </div>

            <div class="mt-24 pt-12 border-t border-slate-100 text-center">
                <p class="text-slate-400 text-xs italic">Thank you for choosing Luxit Global Escapes. We look forward to seeing you soon!</p>
            </div>
        </div>
    </div>

</body>
</html>
