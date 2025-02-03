CREATE TABLE BookCategories (
    BookID INT NOT NULL,                    
    CategoryID INT NOT NULL,                 
    PRIMARY KEY (BookID, CategoryID), 
    FOREIGN KEY (BookID) REFERENCES Books(BookID),
    FOREIGN KEY (CategoryID) REFERENCES Categories(CategoryID)
);