CREATE DATABASE IF NOT EXISTS HOMEAWAYDB;
USE HOMEAWAYDB;

CREATE TABLE User (
    userID VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(30) NOT NULL,
    last_name VARCHAR (30) NOT NULL,
    email VARCHAR(40) UNIQUE NOT NULL,
    phone_number VARCHAR(15) UNIQUE,
    password VARCHAR(30) NOT NULL,
    PRIMARY KEY (userID)
);

CREATE TABLE Address (
    street VARCHAR(200) UNIQUE NOT NULL,
    city VARCHAR(50) NOT NULL,
    postal_code VARCHAR(10) NOT NULL,
    state VARCHAR(30) NOT NULL,
    country VARCHAR(30) NOT NULL,
    PRIMARY KEY (street, postal_code)
);

CREATE TABLE Property (
    propertyID INT AUTO_INCREMENT NOT NULL,
    price DECIMAL(10, 2) NOT NULL, -- price listed as per day?
    type ENUM('Hotel Room', 'Condo', 'Apartment', 'House', 'Villa') NOT NULL, -- could add an option for OTHER where user enters their own type
    num_bedrooms INTEGER NOT NULL,
    num_bathrooms INTEGER NOT NULL,
    max_guests INTEGER NOT NULL,
    street VARCHAR(200) UNIQUE NOT NULL,
    userID VARCHAR(20) NOT NULL,
    PRIMARY KEY (propertyID),
    FOREIGN KEY (street) REFERENCES Address(street) ON DELETE CASCADE,
    FOREIGN KEY (userID) REFERENCES User(userID) ON DELETE CASCADE
    
    -- should add some status flag for each Property row so that it can't be booked if a booking is active
);

CREATE TABLE Booking (
    reservationID INT AUTO_INCREMENT NOT NULL,
    reservation_start DATE NOT NULL,
    reservation_end DATE NOT NULL,
    userID VARCHAR(20) NOT NULL,
    propertyID INT NOT NULL,
    PRIMARY KEY (reservationID),
    FOREIGN KEY (userID) REFERENCES User(userID) ON DELETE CASCADE,
    FOREIGN KEY (propertyID) REFERENCES Property(propertyID) ON DELETE CASCADE
);

CREATE TABLE Review (
    reviewID INT AUTO_INCREMENT NOT NULL,
    content TEXT NOT NULL,
    date DATE NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5), -- rating for Property will be dynamic, enforced at application level
    
    
    propertyID INT NOT NULL,
    userID VARCHAR(20) NOT NULL,
    -- maybe reference reservation instead of property and user separately?
    
    PRIMARY KEY (reviewID),
    FOREIGN KEY (propertyID) REFERENCES Property(propertyID) ON DELETE CASCADE,
    FOREIGN KEY (userID) REFERENCES User(userID) ON DELETE CASCADE
);

CREATE TABLE Photo (
    imageID INT AUTO_INCREMENT NOT NULL,
    propertyID INT NOT NULL,
    image_data TEXT NOT NULL, -- store photo as URL
    PRIMARY KEY (imageID),
    FOREIGN KEY (propertyID) REFERENCES Property(propertyID) ON DELETE CASCADE
);

CREATE TABLE Payment (
    transactionID INT AUTO_INCREMENT NOT NULL,
    payment_method ENUM('VISA', 'MasterCard', 'AMEX', 'Discover', 'PayPal', 'ApplePay', 'GooglePay', 'Bank Transfer', 'ACH') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    date DATE NOT NULL,
    reservationID INT NOT NULL,
    PRIMARY KEY (transactionID),
    FOREIGN KEY (reservationID) REFERENCES Booking(reservationID)
);

CREATE TABLE ShoppingCart (
    userID VARCHAR(20) NOT NULL,
    propertyID INT NOT NULL,
    PRIMARY KEY (userID, propertyID),
    FOREIGN KEY (userID) REFERENCES User(userID) ON DELETE CASCADE,
    FOREIGN KEY (propertyID) REFERENCES Property(propertyID) ON DELETE CASCADE
);

CREATE TABLE PopularBoard (
    date DATE UNIQUE NOT NULL,  -- only one PopularBoard per day shown for all users so no need for boardID; date can uniquely identify
    PRIMARY KEY (date)
    -- display the references to Property objects dynamically at the application level rather than referencing them in SQL
    -- may need to add dynamic counter/ranking system to Property entity to determine which end up on popular board
    -- also need to decide how many entites to put on the popular board at a time
);

CREATE TABLE History (
    historyPageID INT AUTO_INCREMENT NOT NULL,
    userID VARCHAR(20) NOT NULL,
    PRIMARY KEY (historyPageID),
    FOREIGN KEY (userID) REFERENCES User(userID) ON DELETE CASCADE
    -- see comment for PopularBoard 
);


INSERT INTO User (userID, first_name, last_name, email, phone_number, password)
VALUES 
('U001', 'John', 'Doe', 'johndoe@example.com', '1234567890', 'password123'),
('U002', 'Jane', 'Smith', 'janesmith@example.com', '0987654321', 'password456'),
('U003', 'Alice', 'Johnson', 'alicejohnson@example.com', NULL, 'password789');

INSERT INTO Address (street, city, postal_code, state, country)
VALUES 
('123 Elm Street', 'Los Angeles', '12345', 'California', 'United States'),
('456 Oak Avenue', 'New York City', '54321', 'New York', 'United States'),
('789 Smith Drive', 'San Jose', '98765', 'California', 'United States');

INSERT INTO Property (propertyID, price, type, num_bedrooms, num_bathrooms, max_guests, street, userID)
VALUES 
(1, 150.00, 'Condo', 2, 1, 4, '123 Elm Street', 'U001'),
(2, 250.00, 'House', 3, 2, 6, '456 Oak Avenue', 'U002'),
(3, 350.00, 'Villa', 4, 4, 6, '789 Smith Drive', 'U003');

INSERT INTO Booking (reservationID, reservation_start, reservation_end, userID, propertyID)
VALUES 
(1, '2024-12-01', '2024-12-07', 'U001', 1),
(2, '2024-12-10', '2024-12-15', 'U002', 2);

INSERT INTO Review (reviewID, content, date, rating, propertyID, userID)
VALUES 
(1, 'Great place, highly recommend!', '2024-11-10', 5, 1, 'U001'),
(2, 'Decent stay, but could be cleaner.', '2024-11-15', 3, 2, 'U002');

INSERT INTO Photo (imageID, propertyID, image_data)
VALUES 
(1, 1, 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/02/Link-Lee_House_%28HDR%29.jpg/1200px-Link-Lee_House_%28HDR%29.jpg'),
(2, 2, 'https://t4.ftcdn.net/jpg/03/70/64/43/360_F_370644357_MDF4UXLAXTyyi2OyuK66tWW9cA2f8svL.jpg'),
(3, 3, 'https://thumbs.dreamstime.com/b/new-house-4623668.jpg');

INSERT INTO Payment (transactionID, payment_method, amount, date, reservationID)
VALUES 
(1, 'VISA', 1050.00, '2024-11-20', 1),
(2, 'PayPal', 1250.00, '2024-11-22', 2);

INSERT INTO ShoppingCart (userID, propertyID)
VALUES 
('U001', 1),
('U002', 2);

INSERT INTO PopularBoard (date)
VALUES 
('2024-11-01'),
('2024-11-15');

INSERT INTO History (historyPageID, userID)
VALUES 
(1, 'U001'),
(2, 'U003');
