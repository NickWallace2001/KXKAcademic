CREATE TABLE IF NOT EXISTS  `Grades`
(
    id             int auto_increment,
    class          varchar(20),
    grade          varchar(10),
    gpa_hours      int,
    quality_points decimal(10,2),
    semester_id    int,
    user_id        int,
    primary key (id),
    FOREIGN KEY (semester_id) REFERENCES Semesters (id),
    FOREIGN KEY (user_id) REFERENCES KXKUsers (id)
)