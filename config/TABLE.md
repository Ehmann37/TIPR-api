schedule -> stop -> route-information -> route 
                                                > bus -> ticket + payment
                         company -> bus-driver 


-- Company table (optional, since you mentioned company_id)
CREATE TABLE company (
    company_id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(100) NOT NULL
);

-- Bus Driver
CREATE TABLE bus_driver (
    driver_id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    license_number VARCHAR(50),
    contact_info VARCHAR(100),
    FOREIGN KEY (company_id) REFERENCES company(company_id)
);

-- Stop
CREATE TABLE stop (
    stop_id INT AUTO_INCREMENT PRIMARY KEY,
    stop_name VARCHAR(100),
    longitude DECIMAL(10, 7),
    latitude DECIMAL(10, 7)
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
    FOREIGN KEY (route_id) REFERENCES route_information(route_id),
    FOREIGN KEY (stop_id) REFERENCES stop(stop_id)
);

-- Bus
CREATE TABLE bus (
    bus_id INT AUTO_INCREMENT PRIMARY KEY,
    route_id INT,
    company_id INT,
    bus_driver_id INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    FOREIGN KEY (route_id) REFERENCES route_information(route_id),
    FOREIGN KEY (company_id) REFERENCES company(company_id),
    FOREIGN KEY (bus_driver_id) REFERENCES bus_driver(driver_id)
);

-- Payment
CREATE TABLE payment (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT,
    payment_mode VARCHAR(50), -- e.g., cash, card, GCash
    payment_platform VARCHAR(50), -- e.g., terminal, mobile app
    fare_amount DECIMAL(10, 2),
    payment_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Passenger Ticket
CREATE TABLE passenger_ticket (
    ticket_id INT AUTO_INCREMENT PRIMARY KEY,
    bus_id INT,
    origin_stop_id INT,
    destination_stop_id INT,
    payment_id INT,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    seat_number INT,
    passenger_category ENUM('regular', 'student', 'senior', 'pwd'),
    boarding_time DATETIME,
    arrival_time DATETIME,
    ticket_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bus_id) REFERENCES bus(bus_id),
    FOREIGN KEY (origin_stop_id) REFERENCES stop(stop_id),
    FOREIGN KEY (destination_stop_id) REFERENCES stop(stop_id),
    FOREIGN KEY (payment_id) REFERENCES payment(payment_id)
);
