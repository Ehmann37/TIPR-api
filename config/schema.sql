/*
schedule -> stop -> route-information -> route 
                                                > bus -> trip -> payment ->ticket
                    user -> driver + conductor                      
*/

-- Bus Driver
CREATE TABLE driver (
    driver_id INT PRIMARY KEY,
    license_number VARCHAR(50),
    bus_id INT,
    FOREIGN KEY (driver_id) REFERENCES user(user_id)
);

--Conductor
CREATE table conductor (
    conductor_id INT PRIMARY KEY,
    bus_id INT,
    FOREIGN KEY (conductor_id) REFERENCES user(user_id)
);

-- System Administrator
-- CREATE table system_administrator {
    
-- }

-- Operational Managers
-- CREATE table operational_manager {
    
-- }

--bus - routeid
--ticket - origin and dest id\
--trip-routeid



-- Stop
CREATE TABLE stop (
    stop_id INT AUTO_INCREMENT PRIMARY KEY,
    stop_name VARCHAR(100),
    latitude DECIMAL(10, 7)
    longitude DECIMAL(10, 7),
);

-- Schedule
CREATE TABLE schedule (
    schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    first_trip TIME,
    last_trip TIME,
    time_interval INT -- in minutes
);

-- Route Information
CREATE TABLE route_information (
    route_id INT AUTO_INCREMENT PRIMARY KEY,
    route_name VARCHAR(100),
    schedule_id INT,
    FOREIGN KEY (schedule_id) REFERENCES schedule(schedule_id)
);

-- Route: maps stops in a route
CREATE TABLE route (
    route_id INT,
    stop_id INT,
    stop_order INT,
    PRIMARY KEY (route_id, stop_order),
    FOREIGN KEY (stop_id) REFERENCES stop(stop_id)
);

-- Bus
CREATE TABLE bus (
    bus_id INT AUTO_INCREMENT PRIMARY KEY,
    route_id INT DEFAULT NULL,
    driver_id INT DEFAULT NULL,
    conductor_id INT DEFAULT NULL,
    passenger_count INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    FOREIGN KEY (route_id) REFERENCES route_information(route_id),
    FOREIGN KEY (driver_id) REFERENCES driver(driver_id),
    FOREIGN KEY (conductor_id) REFERENCES conductor(conductor_id)
);

-- Payment
CREATE TABLE payment (
    ticket_id INT,
    payment_id VARCHAR(20),
    payment_mode VARCHAR(50),
    payment_platform VARCHAR(50), 
    fare_amount DECIMAL(10, 2),
    payment_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_status ENUM('paid', 'pending') DEFAULT NULL,
    PRIMARY KEY (ticket_id, payment_id),
    FOREIGN KEY (ticket_id) REFERENCES ticket(ticket_id)
);

-- Passenger Ticket
CREATE TABLE ticket (
    ticket_id INT AUTO_INCREMENT PRIMARY KEY,
    bus_id INT,
    origin_stop_id INT,
    destination_stop_id INT,
    full_name VARCHAR(50),
    seat_number VARCHAR(5),
    passenger_category ENUM('regular', 'student', 'senior', 'pwd'),
    passenger_status ENUM('on_bus', 'left_bus'),
    boarding_time DATETIME,
    arrival_time DATETIME,
    ticket_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bus_id) REFERENCES bus(bus_id),
    FOREIGN KEY (origin_stop_id) REFERENCES stop(stop_id),
    FOREIGN KEY (destination_stop_id) REFERENCES stop(stop_id)
);

-- Users 
CREATE TABLE user (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('driver', 'conductor', 'sysAdmin', 'opManagers'),
    full_name VARCHAR(50),
    contact_info VARCHAR(100)
)

-- Trips
CREATE TABLE trip (
    trip_id INT AUTO_INCREMENT PRIMARY KEY,
    route_id INT,
    bus_id INT,
    driver_id INT,
    conductor_id INT,
    boarding_time DATETIME,
    arrival_time DATETIME,
    total_passenger INT,
    total_revenue INT,
    status ENUM('active', 'completed')
    FOREIGN KEY (route_id) REFERENCES route(route_id),
    FOREIGN KEY (bus_id) REFERENCES bus(bus_id),
    FOREIGN KEY (driver_id) REFERENCES user(user_id),
    FOREIGN KEY (conductor_id) REFERENCES user(user_id)
);


-- add trip table
-- add user table with distinct roles
-- remove route and bus driver id attributes on bus table
-- add api for incrementing total passenger as passenger ticket is added
-- api for bus driver and see their current or past trips at given time period
