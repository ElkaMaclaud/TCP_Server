CREATE TABLE BorrowedBooks (
    BorrowID SERIAL PRIMARY KEY,           
    StudentID INT NOT NULL,                 
    BookID INT NOT NULL,                   
    BorrowDate DATE NOT NULL,                -- Дата взятия книги
    ReturnDate DATE,                         -- Дата возврата книги (может быть NULL)
    FOREIGN KEY (StudentID) REFERENCES Students(StudentID),
    FOREIGN KEY (BookID) REFERENCES Books(BookID)
);