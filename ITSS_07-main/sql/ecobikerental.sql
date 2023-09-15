CREATE DATABASE EcoBikeRental;
USE EcoBikeRental;

CREATE TABLE BikeType (
  BikeTypeID INT,
  Name NVARCHAR(50),
  PRIMARY KEY (BikeTypeID)
);

CREATE TABLE PriceList (
  PriceID INT,
  Name NVARCHAR(50),
  Price FLOAT,
  PRIMARY KEY (PriceID)
);

CREATE TABLE ParkingLot (
  ParkingLotID INT,
  Name NVARCHAR(50),
  Address TEXT,
  Area FLOAT,
  PRIMARY KEY (ParkingLotID)
);

CREATE TABLE Bike (
  BikeID INT,
  ParkingLotID INT,
  BikeTypeID INT,
  Price FLOAT,
  Name NVARCHAR(30),
  BatteryStatus NVARCHAR(50),
  Status BIT,
  PRIMARY KEY (BikeID),
  FOREIGN KEY (ParkingLotID) REFERENCES ParkingLot (ParkingLotID),
  FOREIGN KEY (BikeTypeID) REFERENCES BikeType (BikeTypeID)
);

USE EcoBikeRental;
CREATE TABLE TransactionInfo (
  TransactionID INT AUTO_INCREMENT,
  BikeID INT,
  CardID NVARCHAR(20),
  Date TEXT,
  UnlockDate TEXT,
  Deposit FLOAT,
  RentingTime FLOAT,
  Status BIT,
  PaymentMethod INT,
  PRIMARY KEY (TransactionID),
  FOREIGN KEY (BikeID) REFERENCES Bike (BikeID)
);

-- BIKE TYPE
INSERT INTO BikeType
VALUES 
('1',N'Standard bike'),
('2',N'Standard e-bike'),
('3',N'Twin bike');

-- Price List
INSERT INTO PriceList
VALUES 
('1', N'Initial 30-minute rental price', '10000'),
('2', N'Price per 15 minutes', '3000'),
('3', N'Daily rental price', '200000'),
('4', N'Price per hour', '10000'),
('5', N'Late return price per 15 minutes', '2000');

-- PARKING LOT
INSERT INTO ParkingLot
VALUES 
('1',N'Parking Lot A1', N'A1 Street', '10.0'),
('2',N'Parking Lot A2', N'A2 Street', '10.5'),
('3',N'Parking Lot B1', N'B1 Street', '10.0'),
('4',N'Parking Lot B2', N'B2 Street', '10.5');

-- BIKE
INSERT INTO Bike
VALUES 
('1','1','1','100000', N'Standard bike 1', N'None',b'0'),
('2','1','1','200000', N'Standard bike 2', N'None',b'0'),
('3','2','2','300000', N'Standard e-bike 1', N'11 hours',b'0'),
('4','2','2','400000', N'Standard e-bike 2', N'8 hours',b'0'),
('5', '3', '2', '100000', N'Standard e-bike 3', N'10 hours', b'0'),
('6', '3', '3', '200000', N'Twin Bike 1', N'None', b'0'),
('7', '4', '3', '200000', N'Twin Bike 2', N'None', b'0'),
('8', '3', '2', '400000', N'Standard e-bike 4', N'13 hours', b'0');

-- check
SELECT * FROM Bike;
SELECT * FROM ParkingLot;
SELECT * FROM BikeType;
SELECT * FROM PriceList;
SELECT * FROM TransactionInfo;
