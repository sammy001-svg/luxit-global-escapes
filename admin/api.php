<?php
header('Content-Type: application/json');
require_once '../includes/db.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_all':
        $data = [
            'tours' => $pdo->query("SELECT * FROM tours")->fetchAll(),
            'destinations' => $pdo->query("SELECT * FROM destinations")->fetchAll(),
            'bookings' => $pdo->query("SELECT * FROM bookings")->fetchAll(),
            'customers' => $pdo->query("SELECT * FROM customers")->fetchAll(),
            'analytics' => [
                'totalBookings' => $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn(),
                'totalRevenue' => $pdo->query("SELECT SUM(amount) FROM bookings WHERE status='Confirmed'")->fetchColumn() ?: 0,
                'activeTours' => $pdo->query("SELECT COUNT(*) FROM tours WHERE status='Active'")->fetchColumn(),
                'newCustomers' => $pdo->query("SELECT COUNT(*) FROM customers WHERE joined_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn(),
                'revenueData' => [5000, 7500, 6200, 8900, 11000, 15000, 13500],
                'bookingTrend' => [25, 40, 35, 55, 70, 95, 80]
            ],
            'activityFeed' => $pdo->query("SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 10")->fetchAll(),
            'settings' => []
        ];
        
        $settings_raw = $pdo->query("SELECT * FROM settings")->fetchAll();
        foreach ($settings_raw as $s) {
            $data['settings'][$s['setting_key']] = $s['setting_value'];
        }
        
        echo json_encode($data);
        break;

    case 'save_tour':
        $raw = file_get_contents('php://input');
        $tour = json_decode($raw, true);
        
        if (isset($tour['id'])) {
            $stmt = $pdo->prepare("UPDATE tours SET title=?, location=?, price=?, duration=?, rating=?, status=?, category=?, image=?, description=? WHERE id=?");
            $stmt->execute([$tour['title'], $tour['location'], $tour['price'], $tour['duration'], $tour['rating'], $tour['status'], $tour['category'], $tour['image'], $tour['description'], $tour['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO tours (title, location, price, duration, rating, status, category, image, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tour['title'], $tour['location'], $tour['price'], $tour['duration'], $tour['rating'], $tour['status'], $tour['category'], $tour['image'], $tour['description']]);
        }
        echo json_encode(['success' => true]);
        break;

    case 'delete_tour':
        $id = $_GET['id'] ?? 0;
        $stmt = $pdo->prepare("DELETE FROM tours WHERE id=?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    case 'save_settings':
        $raw = file_get_contents('php://input');
        $settings = json_decode($raw, true);
        foreach ($settings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value=?");
            $stmt->execute([$key, $value, $value]);
        }
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?>
