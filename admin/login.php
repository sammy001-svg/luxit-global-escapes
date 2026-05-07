<?php
session_start();

// If already logged in, redirect to index
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../includes/db.php';
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            header("Location: index.php");
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    } else {
        $error = 'Please enter both username and password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxit Admin - Login</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#006A72',
                        secondary: '#FFD214',
                        dark: {
                            800: '#1E293B',
                            900: '#0F172A',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-dark-900 text-slate-100 flex items-center justify-center h-screen relative overflow-hidden">
    
    <!-- Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-primary rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
        <div class="absolute top-40 -right-40 w-96 h-96 bg-secondary rounded-full mix-blend-multiply filter blur-3xl opacity-10"></div>
    </div>

    <div class="glass relative z-10 w-full max-w-md p-8 rounded-2xl border border-white/10 shadow-2xl">
        <div class="text-center mb-8">
            <img src="../assets/images/luxit-africa-logo.png" alt="Luxit Logo" class="h-12 mx-auto mb-4">
            <h1 class="text-2xl font-bold tracking-tight">Admin Login</h1>
            <p class="text-slate-400 text-sm mt-2">Enter your credentials to access the dashboard</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/50 text-red-500 px-4 py-3 rounded-xl mb-6 text-sm flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-slate-300 mb-2">Username</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-slate-500"></i>
                    </div>
                    <input type="text" name="username" id="username" required class="block w-full pl-10 pr-3 py-2.5 bg-dark-800/50 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" placeholder="admin">
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-slate-500"></i>
                    </div>
                    <input type="password" name="password" id="password" required class="block w-full pl-10 pr-3 py-2.5 bg-dark-800/50 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center text-slate-400 hover:text-white cursor-pointer transition">
                    <input type="checkbox" class="rounded border-white/10 bg-dark-800 text-primary focus:ring-primary mr-2"> Remember me
                </label>
                <a href="#" class="text-primary hover:text-secondary transition">Forgot Password?</a>
            </div>

            <button type="submit" class="w-full bg-primary hover:bg-opacity-90 text-white font-semibold py-3 px-4 rounded-xl transition duration-300 flex justify-center items-center group">
                Sign In
                <i class="fas fa-arrow-right ml-2 opacity-0 group-hover:opacity-100 transform -translate-x-2 group-hover:translate-x-0 transition-all"></i>
            </button>
        </form>
    </div>
</body>
</html>
