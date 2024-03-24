CREATE TABLE Address (
    address_ID INT AUTO_INCREMENT PRIMARY KEY,
    street_num VARCHAR(10),
    street_name VARCHAR(100),
    city VARCHAR(100),
    state VARCHAR(100),
    zipcode VARCHAR(10)
);

CREATE TABLE User (
    user_ID INT AUTO_INCREMENT PRIMARY KEY,
    age INT,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(50),
    address INT,
    CHECK (age >= 14),
    FOREIGN KEY (address) REFERENCES Address(address_ID) ON DELETE CASCADE
);

CREATE TABLE Store (
    store_ID INT AUTO_INCREMENT PRIMARY KEY,
    address INT,
    name VARCHAR(100),
    store_category VARCHAR(100),
    FOREIGN KEY (address) REFERENCES Address(address_ID) ON DELETE CASCADE
);

CREATE TABLE Item (
    item_ID INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255),
    description TEXT,
    name VARCHAR(100),
    brand VARCHAR(100),
    item_category VARCHAR(100)
);

CREATE TABLE Review (
    review_ID INT AUTO_INCREMENT PRIMARY KEY,
    user INT,
    item INT,
    store INT,
    image VARCHAR(255),
    comment TEXT,
    rating INT,
    review_time DATETIME,
    FOREIGN KEY (user) REFERENCES User(user_ID) ON DELETE CASCADE,
    FOREIGN KEY (item) REFERENCES Item(item_ID) ON DELETE CASCADE,
    FOREIGN KEY (store) REFERENCES Store(store_ID) ON DELETE CASCADE
);

CREATE TABLE Membership (
    user INT,
    store INT,
    is_VIP BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user) REFERENCES User(user_ID) ON DELETE CASCADE,
    FOREIGN KEY (store) REFERENCES Store(store_ID) ON DELETE CASCADE,
    PRIMARY KEY (user, store)
);

CREATE TABLE StoreItems (
    store INT,
    item INT,
    price DECIMAL(10, 2),
    weight DECIMAL(10, 3),
    unit VARCHAR(10),
    price_per_unit DECIMAL(10, 2),
    CHECK (price>0 AND weight>0 AND unit>0 AND price_per_unit>0),
    FOREIGN KEY (store) REFERENCES Store(store_ID) ON DELETE CASCADE,
    FOREIGN KEY (item) REFERENCES Item(item_ID) ON DELETE CASCADE,
    PRIMARY KEY (store, item)
);

CREATE TABLE PurchaseHistory (
    purchase_ID INT AUTO_INCREMENT PRIMARY KEY,
    user INT,
    item INT,
    quantity INT,
    purchase_time DATETIME,
    FOREIGN KEY (user) REFERENCES User(user_ID) ON DELETE CASCADE,
    FOREIGN KEY (item) REFERENCES Item(item_ID) ON DELETE CASCADE
);

CREATE TABLE Favorites (
    user INT,
    item INT,
    store INT,
    added_date DATETIME,
    notification_enabled BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user) REFERENCES User(user_ID) ON DELETE CASCADE,
    FOREIGN KEY (item) REFERENCES Item(item_ID) ON DELETE CASCADE,
    FOREIGN KEY (store) REFERENCES Store(store_ID) ON DELETE CASCADE,
    PRIMARY KEY (user, item, store)
);

CREATE TABLE NotifySale (
    user INT,
    item INT,
    store INT,
    notification_type VARCHAR(100),
    last_notified DATETIME,
    FOREIGN KEY (user) REFERENCES User(user_ID) ON DELETE CASCADE,
    FOREIGN KEY (item) REFERENCES Item(item_ID) ON DELETE CASCADE,
    FOREIGN KEY (store) REFERENCES Store(store_ID) ON DELETE CASCADE,
    PRIMARY KEY (user, item, store)
);

CREATE TABLE Sale (
    sale_ID INT AUTO_INCREMENT PRIMARY KEY,
    item INT,
    store INT,
    start_date DATETIME,
    end_date DATETIME,
    sale_price DECIMAL(10, 2),
    CHECK (end_date > start_date),
    FOREIGN KEY (item) REFERENCES Item(item_ID) ON DELETE CASCADE,
    FOREIGN KEY (store) REFERENCES Store(store_ID) ON DELETE CASCADE
);

CREATE TABLE CHANGE_REQUEST (
    request_ID INT AUTO_INCREMENT PRIMARY KEY,
    user INT,
    item INT,
    store INT,
    request_time DATETIME,
    change_details VARCHAR(1000),
    accepted BOOL
    FOREIGN KEY (user) REFERENCES User(user_ID) ON DELETE CASCADE,
    FOREIGN KEY (item) REFERENCES Item(item_ID) ON DELETE CASCADE,
    FOREIGN KEY (store) REFERENCES Store(store_ID) ON DELETE CASCADE,
);




---------- USER ----------
-- User registation
INSERT INTO User (age, first_name, last_name, email, password, address) 
VALUES ($age, $first_name, $last_name, $email, $password, $address);

-- Check user password
SELECT user_ID FROM User 
WHERE email = $email AND password = $password;

-- Update user profile
UPDATE User SET first_name = $first_name, last_name = $last_name, email = $email
WHERE user_ID = $user_ID;

-- Update user password
UPDATE User SET password = $password WHERE user_ID = $user_ID;

-- Delete an address
DELETE FROM Address WHERE address_ID = $address_ID;

---------- ADDRESS ----------
-- Add an address
INSERT INTO Address (street_num, street_name, city, state, zipcode)
VALUES ($street_num, $street_name, $city, $state, $zipcode);

-- Change an address
UPDATE Address
SET street_num = $street_num, street_name = $street_name, city = $city, state = $state, zipcode = $zipcode
WHERE address_ID = $address_ID;


---------- FAVORITES ----------
-- To get a user's favorites when loading their favorites list
SELECT * FROM favorites WHERE user = $user_ID;

-- Add to a user's favorites list
INSERT INTO Favorites (user, item, store, added_date, notification_enabled) 
VALUES ($user, $item, $store, $added_date, $notification_enabled);

-- Get recommendations for a user based on their favorites
SELECT Item.* 
FROM Item 
JOIN Favorites ON Item.item_ID = Favorites.item 
WHERE Favorites.user = $user_ID LIMIT 10;


---------- REVIEW ----------

-- Add a new review
INSERT INTO Review (user, item, store, image, comment, rating, review_time) 
VALUES ($user, $item, $store, $image, $comment, $rating, $review_time);

-- Edit a review
UPDATE Review SET comment = $comment, rating = $rating 
WHERE review_ID = $review_ID;

-- Delete a review
DELETE FROM Review WHERE review_ID = $review_ID;

-- Get all reviews for a product
SELECT * FROM Review WHERE item = $item_ID ORDER BY review_time DESC;

---------- SEARCH RESULTS ----------

-- Search for an item (e.g. milk)
SELECT * FROM Item WHERE name LIKE '%milk%';

-- Sort items by price (e.g. milk)
SELECT Item.*, StoreItems.price 
FROM Item 
JOIN StoreItems ON Item.item_ID = StoreItems.item 
WHERE Item.name LIKE '%milk%' 
ORDER BY StoreItems.price ASC;

SELECT Item.*, StoreItems.price 
FROM Item 
JOIN StoreItems ON Item.item_ID = StoreItems.item 
WHERE Item.name LIKE '%milk%' 
ORDER BY StoreItems.price DESC;

-- Filter search by category
SELECT * FROM Item WHERE name LIKE '%milk%' AND item_category = $category;


---------- SALE NOTIFICATION ----------

-- Subscribe to sale notification
INSERT INTO NotifySale (user, item, store, notification_type, last_notified) 
VALUES ($user, $item, $store, $notification_type, $last_notified);


---------- ITEM ----------

-- Get details of product
SELECT * FROM Item WHERE item_ID = $item_ID;

-- Add an item
INSERT INTO Item (image, description, name, brand, item_category)
VALUES ($image, $description, $name, $brand, $item_category);

-- Change an item info
UPDATE Item
SET name = $name, description = $description, brand = $brand, item_category = $item_category
WHERE item_ID = $item_ID;

-- Delete an item
DELETE FROM Item WHERE item_ID = $item_ID;


---------- STORE ITEMS ----------
-- Add item to a store
INSERT INTO StoreItems (store, item, price, weight, unit, price_per_unit)
VALUES ($tore, $item, $price, $weight, $unit, $price_per_unit);

-- Edit item from a store
UPDATE StoreItems
SET price = $price, weight = $weight, unit = $unit, price_per_unit = $price_per_unit
WHERE store = $store_ID AND item = $item_ID;

-- Delete item from a store
DELETE FROM StoreItems WHERE store = $store_ID AND item = $item_ID;

-- Select all items from a store
SELECT Item.*
FROM Item
JOIN StoreItems ON Item.item_ID = StoreItems.item
WHERE StoreItems.store = $store_ID;

-- Select all stores that have some item
SELECT Store.*
FROM Store
JOIN StoreItems ON Store.store_ID = StoreItems.store
WHERE StoreItems.item = $item_ID;

------- Sample Data -------
INSERT INTO Address (street_num, street_name, city, state, zipcode) VALUES
('1050', 'Jefferson Ave', 'Redwood City', 'CA', '94063'),
('204', 'Baker St', 'San Francisco', 'CA', '94107'),
('1560', 'Lincoln Ave', 'Pasadena', 'CA', '91103'),
('230', 'Market St', 'San Jose', 'CA', '95110'),
('748', 'Ames St', 'Baldwin Park', 'CA', '91706'),
('322', 'Fifth Ave', 'Los Angeles', 'CA', '90003'),
('1987', 'Main St', 'Irvine', 'CA', '92614'),
('450', 'Broadway St', 'San Diego', 'CA', '92101'),
('77', 'Vine St', 'Hollywood', 'CA', '90028'),
('605', 'Sunset Blvd', 'Santa Monica', 'CA', '90401');

INSERT INTO User (age, first_name, last_name, email, password, address) VALUES
(34, 'Alicia', 'Ramirez', 'alicia.ram@example.com', 'pass123!', 1),
(29, 'Ben', 'Chen', 'ben.chen88@example.com', 'mySecurePass$', 2),
(42, 'Carla', 'Diaz', 'carladz@example.com', 'diaz1234', 3),
(31, 'David', 'Smith', 'dav.smith@example.com', 'smithPass', 4),
(27, 'Elena', 'Morales', 'elena_m@example.com', 'moralesSecure', 5),
(36, 'Frank', 'Gupta', 'fgupta@example.com', 'frank2024!', 6),
(24, 'Gina', 'Huang', 'gina_huang@example.com', 'passwordGina', 7),
(39, 'Harold', 'Kim', 'h.kim@example.com', 'kimPass123', 8),
(28, 'Iris', 'Johnson', 'irisj@example.com', 'irisSecure!', 9),
(45, 'Jake', 'Martinez', 'jake.m@example.com', 'jakePass45', 10);

INSERT INTO Store (address, name, store_category) VALUES
(1, 'Fresh Farm Produce', 'Grocery'),
(2, 'Everyday Needs', 'Convenience'),
(3, 'Tech Gadgets Central', 'Electronics'),
(4, 'Homemade Bakery', 'Food'),
(5, 'Quick Mart', 'Convenience'),
(6, 'Green Grocers', 'Grocery'),
(7, 'Book Haven', 'Books'),
(8, 'Gadget World', 'Electronics'),
(9, 'Bite and Brew', 'Cafe'),
(10, 'Pampered Pets', 'Pet Supplies');

INSERT INTO Item (image, description, name, brand, item_category) VALUES
('img/apple.jpg', 'Fresh organic apples from local farms.', 'Organic Apples', 'Farm Fresh', 'Fruits'),
('img/bread.jpg', 'Whole wheat bread, baked fresh daily.', 'Whole Wheat Bread', 'Homemade Bakery', 'Bakery'),
('img/coffee_maker.jpg', '12-cup programmable coffee maker with auto shut-off.', 'Coffee Maker', 'BrewPlus', 'Electronics'),
('img/dog_food.jpg', 'Natural dog food with no artificial colors or flavors.', 'Natural Dog Food', 'HealthyPet', 'Pet Food'),
('img/ebook_reader.jpg', 'E-book reader with built-in light and weeks-long battery life.', 'E-Book Reader', 'ReadWell', 'Electronics'),
('img/flowers.jpg', 'A bouquet of mixed flowers, perfect for any occasion.', 'Mixed Flowers Bouquet', 'BloomBox', 'Home & Garden'),
('img/green_tea.jpg', 'Organic green tea with a refreshing taste.', 'Green Tea', 'TeaHeaven', 'Beverages'),
('img/hand_sanitizer.jpg', 'Kills 99.99% of germs without water.', 'Hand Sanitizer', 'CleanHands', 'Health & Beauty'),
('img/ice_cream.jpg', 'Vanilla ice cream made with real cream and vanilla beans.', 'Vanilla Ice Cream', 'CreamyDream');

INSERT INTO Review (user, item, store, image, comment, rating, review_time) VALUES
(1, 5, 3, 'img/review_coffee_maker.jpg', 'Great coffee maker for the price!', 4, NOW()),
(2, 1, 6, 'img/review_apples.jpg', 'The freshest apples I have bought!', 5, NOW()),
(3, 4, 10, 'img/review_dog_food.jpg', 'My dog loves this food.', 5, NOW()),
(4, 7, 5, 'img/review_green_tea.jpg', 'Very refreshing tea. Will buy again.', 4, NOW()),
(5, 8, 4, 'img/review_sanitizer.jpg', 'Effective and smells nice.', 4, NOW()),
(6, 9, 2, 'img/review_ice_cream.jpg', 'Best vanilla ice cream out there!', 5, NOW()),
(7, 2, 4, 'img/review_bread.jpg', 'Bread was very fresh and tasty.', 5, NOW()),
(8, 3, 8, 'img/review_ebook_reader.jpg', 'Love my new e-book reader!', 5, NOW()),
(9, 6, 9, 'img/review_flowers.jpg', 'Beautiful bouquet, lasted a long time.', 5, NOW()),
(10, 10, 1, 'img/review_pet_shampoo.jpg', 'Great for sensitive skin.', 4, NOW());

INSERT INTO Membership (user, store, is_VIP) VALUES
(1, 1, FALSE),
(2, 2, TRUE),
(3, 3, TRUE),
(4, 4, FALSE),
(5, 5, TRUE),
(6, 6, FALSE),
(7, 7, TRUE),
(8, 8, FALSE),
(9, 9, TRUE),
(10, 10, FALSE);

INSERT INTO StoreItems (store, item, price, weight, unit, price_per_unit) VALUES
(1, 1, 2.99, 1, 'kg', 2.99),
(2, 2, 4.50, 0.5, 'kg', 9.00),
(3, 3, 29.99, 1, 'pc', 29.99),
(4, 4, 19.99, 15, 'kg', 1.33),
(5, 5, 79.99, 1, 'pc', 79.99),
(6, 6, 12.99, 1, 'pc', 12.99),
(7, 7, 3.99, 0.1, 'kg', 39.90),
(8, 8, 1.99, 0.25, 'L', 7.96),
(9, 9, 5.99, 0.5, 'kg', 11.98),
(10, 10, 29.99, 1, 'pc', 29.99);

INSERT INTO PurchaseHistory (user, item, quantity, purchase_time) VALUES
(1, 5, 1, NOW()),
(2, 2, 2, NOW()),
(3, 8, 3, NOW()),
(4, 4, 1, NOW()),
(5, 1, 5, NOW()),
(6, 7, 2, NOW()),
(7, 3, 1, NOW()),
(8, 6, 1, NOW()),
(9, 9, 2, NOW()),
(10, 10, 1, NOW());

INSERT INTO Favorites (user, item, store, added_date, notification_enabled) VALUES
(1, 3, 3, NOW(), TRUE),
(2, 5, 5, NOW(), FALSE),
(3, 2, 2, NOW(), TRUE),
(4, 1, 1, NOW(), FALSE),
(5, 4, 4, NOW(), TRUE),
(6, 7, 7, NOW(), FALSE),
(7, 8, 8, NOW(), TRUE),
(8, 9, 9, NOW(), FALSE),
(9, 6, 6, NOW(), TRUE),
(10, 10, 10, NOW(), FALSE);

INSERT INTO NotifySale (user, item, store, notification_type, last_notified) VALUES
(1, 1, 1, 'Price Drop', NOW()),
(2, 2, 2, 'New Stock', NOW()),
(3, 3, 3, 'Price Drop', NOW()),
(4, 4, 4, 'New Stock', NOW()),
(5, 5, 5, 'Price Drop', NOW()),
(6, 6, 6, 'New Stock', NOW()),
(7, 7, 7, 'Price Drop', NOW()),
(8, 8, 8, 'New Stock', NOW()),
(9, 9, 9, 'Price Drop', NOW()),
(10, 10, 10, 'New Stock', NOW());

INSERT INTO Sale (item, store, start_date, end_date, sale_price) VALUES
(1, 1, '2024-04-01 00:00:00', '2024-04-07 23:59:59', 1.99),
(2, 2, '2024-05-01 00:00:00', '2024-05-07 23:59:59', 3.99),
(3, 3, '2024-06-01 00:00:00', '2024-06-07 23:59:59', 24.99),
(4, 4, '2024-07-01 00:00:00', '2024-07-07 23:59:59', 17.99),
(5, 5, '2024-08-01 00:00:00', '2024-08-07 23:59:59', 69.99),
(6, 6, '2024-09-01 00:00:00', '2024-09-07 23:59:59', 9.99),
(7, 7, '2024-10-01 00:00:00', '2024-10-07 23:59:59', 2.99),
(8, 8, '2024-11-01 00:00:00', '2024-11-07 23:59:59', 1.49),
(9, 9, '2024-12-01 00:00:00', '2024-12-07 23:59:59', 4.99),
(10, 10, '2025-01-01 00:00:00', '2025-01-07 23:59:59', 24.99);

INSERT INTO CHANGE_REQUEST (user, item, store, request_time, change_details, accepted) VALUES
(1, 1, 1, NOW(), 'Update item description.', FALSE),
(2, 2, 2, NOW(), 'Change item price to match competitor.', TRUE),
(3, 3, 3, NOW(), 'Request to add new item colors.', FALSE),
(4, 4, 4, NOW(), 'Request for bulk purchase discount.', TRUE),
(5, 5, 5, NOW(), 'Update product images.', FALSE),
(6, 6, 6, NOW(), 'Change warranty terms.', TRUE),
(7, 7, 7, NOW(), 'Add customer reviews section.', FALSE),
(8, 8, 8, NOW(), 'Update stock levels.', TRUE),
(9, 9, 9, NOW(), 'Request for product demos.', FALSE),
(10, 10, 10, NOW(), 'Add additional product specifications.', TRUE);