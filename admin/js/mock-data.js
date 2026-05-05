window.MOCK_DATA = {
    tours: [
        { id: 1, title: "Bali Luxury Escape", location: "Bali, Indonesia", price: 472, duration: "8 Days / 3 Nights", rating: 4.8, status: "Active", category: "Luxury", image: "assets/images/tour/style1/pic1.jpg", description: "Nusa Penida is a stunning island located just southeast of Bali, offering dramatic cliffs and pristine beaches." },
        { id: 2, title: "South Korea Mountain Trek", location: "South Korea", price: 300, duration: "4 Days / 2 Nights", rating: 4.8, status: "Active", category: "Adventure", image: "assets/images/tour/style1/pic2.jpg", description: "Deogyusan mountain. Its highest peak is 1,614 m above sea level, perfect for sunrise views." },
        { id: 3, title: "Tokyo City Highlights", location: "Tokyo, Japan", price: 594, duration: "6 Days / 3 Nights", rating: 4.8, status: "Active", category: "Culture", image: "assets/images/tour/style1/pic3.jpg", description: "The bridge offers panoramic views of Tokyo Tower and the stunning city skyline." },
        { id: 4, title: "Plateau in Slovenia", location: "Slovenia", price: 1192, duration: "8 Days / 3 Nights", rating: 4.8, status: "Active", category: "Adventure", image: "assets/images/tour/style1/pic4.jpg", description: "Discover the emerald lakes and scenic plateaus of the Slovenian Alps." },
        { id: 5, title: "Switzerland Tour Package", location: "Switzerland", price: 516, duration: "4 Days / 2 Nights", rating: 4.8, status: "Active", category: "Luxury", image: "assets/images/tour/style1/pic5.jpg", description: "Experience the magic of the Swiss Alps with premium rail journeys and mountain retreats." },
        { id: 6, title: "Tokyo City (Standard)", location: "Tokyo, Japan", price: 474, duration: "6 Days / 3 Nights", rating: 4.8, status: "Active", category: "Culture", image: "assets/images/tour/style1/pic6.jpg", description: "Explore the bustling streets and rich heritage of Tokyo with our budget-friendly package." },
        { id: 7, title: "Majestic Iceland", location: "Reykjavik, Iceland", price: 1800, duration: "5 Days", rating: 4.8, status: "Active", category: "Adventure", image: "assets/images/tour/style1/pic1.jpg", description: "The Land of Fire and Ice awaits you with volcanoes, glaciers, and Northern Lights." },
        { id: 8, title: "Maldives Paradise", location: "Male, Maldives", price: 2500, duration: "7 Days", rating: 5.0, status: "Active", category: "Beach", image: "assets/images/tour/style1/pic4.jpg", description: "Book for 2027 and lock in today's prices for our most exclusive island retreat." }
    ],
    destinations: [
        { id: 1, name: "Kenya", region: "Africa", toursCount: 12, visits: 1250 },
        { id: 2, name: "Indonesia", region: "Asia", toursCount: 8, visits: 3400 },
        { id: 3, name: "Switzerland", region: "Europe", toursCount: 5, visits: 2100 },
        { id: 4, name: "Japan", region: "Asia", toursCount: 6, visits: 4500 },
        { id: 5, name: "UAE", region: "Middle East", toursCount: 15, visits: 6000 },
        { id: 6, name: "Greece", region: "Europe", toursCount: 10, visits: 5200 },
        { id: 7, name: "China", region: "Asia", toursCount: 20, visits: 8000 },
        { id: 8, name: "Brazil", region: "South America", toursCount: 4, visits: 1500 },
        { id: 9, name: "USA", region: "North America", toursCount: 25, visits: 10000 },
        { id: 10, name: "Egypt", region: "Africa", toursCount: 9, visits: 3100 }
    ],
    bookings: [
        { id: "BK-1001", user: "John Doe", email: "john@example.com", tour: "Safari in Kenya", date: "2026-05-15", amount: 1200, status: "Confirmed" },
        { id: "BK-1002", user: "Alice Smith", email: "alice@example.com", tour: "Bali Luxury Escape", date: "2026-06-10", amount: 1700, status: "Pending" },
        { id: "BK-1003", user: "Bob Wilson", email: "bob@example.com", tour: "Swiss Alps Adventure", date: "2026-05-20", amount: 1500, status: "Confirmed" },
        { id: "BK-1004", user: "Emma Davis", email: "emma@example.com", tour: "Dubai Desert Safari", date: "2026-04-25", amount: 450, status: "Cancelled" },
        { id: "BK-1005", user: "Mike Brown", email: "mike@example.com", tour: "Safari in Kenya", date: "2026-07-01", amount: 2400, status: "Pending" },
        { id: "BK-1006", user: "Sarah Johnson", email: "sarah@example.com", tour: "Santorini Sunset Bliss", date: "2026-08-12", amount: 1100, status: "Confirmed" },
        { id: "BK-1007", user: "David Lee", email: "david@example.com", tour: "Great Wall Expedition", date: "2026-05-05", amount: 800, status: "Pending" },
        { id: "BK-1008", user: "Laura Garcia", email: "laura@example.com", tour: "Iceland Northern Lights", date: "2026-11-15", amount: 3600, status: "Confirmed" },
        { id: "BK-1009", user: "Kevin Chen", email: "kevin@example.com", tour: "Maldives Private Island", date: "2026-06-20", amount: 5000, status: "Pending" },
        { id: "BK-1010", user: "Olivia White", email: "olivia@example.com", tour: "Kyoto Heritage Tour", date: "2026-05-10", amount: 950, status: "Confirmed" }
    ],
    customers: [
        { id: "CU-001", name: "John Doe", email: "john@example.com", country: "USA", bookings: 3, joined: "2025-10-01" },
        { id: "CU-002", name: "Alice Smith", email: "alice@example.com", country: "UK", bookings: 1, joined: "2026-01-15" },
        { id: "CU-003", name: "Bob Wilson", email: "bob@example.com", country: "Canada", bookings: 5, joined: "2025-05-20" },
        { id: "CU-004", name: "Emma Davis", email: "emma@example.com", country: "Australia", bookings: 2, joined: "2026-02-10" },
        { id: "CU-005", name: "Sarah Johnson", email: "sarah@example.com", country: "Germany", bookings: 4, joined: "2025-11-30" }
    ],
    analytics: {
        totalRevenue: 38200,
        currentMonthIncome: 12450,
        totalBookings: 245,
        newBookingsToday: 8,
        activeTours: 68,
        popularTours: [
            { name: "Safari in Kenya", bookings: 72 },
            { name: "Bali Luxury Escape", bookings: 54 },
            { name: "Maldives Private Island", bookings: 48 },
            { name: "Santorini Sunset Bliss", bookings: 42 }
        ],
        monthlyStats: [
            { month: "Jan", revenue: 4000 },
            { month: "Feb", revenue: 3500 },
            { month: "Mar", revenue: 5500 },
            { month: "Apr", revenue: 8000 },
            { month: "May", revenue: 9500 },
            { month: "Jun", revenue: 12000 }
        ]
    },
    events: [
        { id: 1, title: "Safari Group Departure", date: "2026-04-15", type: "tour" },
        { id: 2, title: "Staff Meeting", date: "2026-04-18", type: "meeting" },
        { id: 3, title: "Bali Package Launch", date: "2026-04-20", type: "promo" },
        { id: 4, title: "Customer Appreciation Day", date: "2026-04-25", type: "event" },
        { id: 5, title: "Iceland Expedition Starts", date: "2026-05-01", type: "tour" }
    ],
    activityFeed: [
        { id: 1, user: "Admin", action: "Added new tour", target: "Iceland Northern Lights", time: "2 hours ago" },
        { id: 2, user: "System", action: "New Booking", target: "BK-1011", time: "4 hours ago" },
        { id: 3, user: "John Doe", action: "Left a review", target: "Safari in Kenya", time: "1 day ago" }
    ],
    settings: {
        agencyName: "Luxit Global Escapes",
        contactEmail: "info@luxitglobalescapes.com",
        contactPhone: "+254 737 800 900",
        currency: "USD",
        timezone: "UTC+3",
        maintenanceMode: false,
        theme: "dark"
    }
};

// End of file (export removed for global compatibility)
