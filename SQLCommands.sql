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