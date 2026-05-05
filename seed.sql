USE luxit_travel;

-- Seed Destinations
INSERT INTO destinations (name, region, description, image) VALUES 
('Kenya', 'Africa', 'The home of the safari.', 'assets/images/dest/kenya.jpg'),
('Indonesia', 'Asia', 'Tropical paradise.', 'assets/images/dest/indonesia.jpg'),
('Switzerland', 'Europe', 'The heart of the Alps.', 'assets/images/dest/switzerland.jpg'),
('Japan', 'Asia', 'Land of the rising sun.', 'assets/images/dest/japan.jpg'),
('UAE', 'Middle East', 'Modern marvels.', 'assets/images/dest/uae.jpg');

-- Seed Tours
INSERT INTO tours (title, location, price, duration, rating, status, category, image, description, show_on_home, home_section) VALUES 
('Bali Luxury Escape', 'Bali, Indonesia', 472.00, '8 Days / 3 Nights', 4.8, 'Active', 'Luxury', 'assets/images/tour/style1/pic1.jpg', 'Nusa Penida is a stunning island located just southeast of Bali.', TRUE, 'Explore Popular Tours'),
('South Korea Mountain Trek', 'South Korea', 300.00, '4 Days / 2 Nights', 4.8, 'Active', 'Adventure', 'assets/images/tour/style1/pic2.jpg', 'Deogyusan mountain peak views.', TRUE, 'Explore Popular Tours'),
('Tokyo City Highlights', 'Tokyo, Japan', 594.00, '6 Days / 3 Nights', 4.8, 'Active', 'Culture', 'assets/images/tour/style1/pic3.jpg', 'Panoramic views of Tokyo Tower.', TRUE, 'Explore Popular Tours'),
('Safari in Kenya', 'Kenya', 1200.00, '5 Days / 4 Nights', 4.9, 'Active', 'Safari', 'assets/images/tour/style1/pic1.jpg', 'Experience the Wild Heart of Africa.', TRUE, 'Main Header Slider'),
('Maldives Paradise', 'Male, Maldives', 2500.00, '7 Days', 5.0, 'Active', 'Beach', 'assets/images/tour/style1/pic4.jpg', 'Paradise Found in the Maldives.', TRUE, 'Main Header Slider');

-- Seed Customers
INSERT INTO customers (name, email, country, joined_date) VALUES 
('John Doe', 'john@example.com', 'USA', '2025-10-01'),
('Alice Smith', 'alice@example.com', 'UK', '2026-01-15');

-- Seed Bookings
INSERT INTO bookings (id, customer_id, tour_id, travel_date, amount, status) VALUES 
('BK-1001', 1, 4, '2026-05-15', 1200.00, 'Confirmed'),
('BK-1002', 2, 1, '2026-06-10', 1700.00, 'Pending');
