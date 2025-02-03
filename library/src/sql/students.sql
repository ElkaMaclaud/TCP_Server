CREATE TABLE Students (
    StudentID SERIAL PRIMARY KEY,           
    FirstName VARCHAR(50) NOT NULL,         
    LastName VARCHAR(50) NOT NULL,  
    DateOfBirth DATE,  
    Address TEXT  
);