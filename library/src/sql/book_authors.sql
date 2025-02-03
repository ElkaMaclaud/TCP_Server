CREATE TABLE BookAuthors (
    BookID INT NOT NULL,                     
    AuthorID INT NOT NULL,                  
    PRIMARY KEY (BookID, AuthorID),         
    FOREIGN KEY (BookID) REFERENCES Books(BookID),
    FOREIGN KEY (AuthorID) REFERENCES Authors(AuthorID)
);