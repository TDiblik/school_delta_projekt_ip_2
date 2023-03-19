-- Bad way of achieving gender independent job titles, however since gender of employee is not specified, I don't care lol
-- Better solution would be to have three columns, one for male, one for female and one for they/them/other and then, using procedure, select column based on employee's gender.
drop table if exists job_titles;
create table job_titles (
    id varchar(36) not null primary key default (uuid()),
    name nvarchar(50) not null unique
);
insert into job_titles (name) values ('ekonom(ka)'), ('techni(k/čka)'), ('skladní(k/ce)'), ('ředitel(ka)'), ('mistr(yně)');
-- select * from job_title;

drop table if exists rooms;
create table rooms (
    id varchar(36) not null primary key default (uuid()),
    name nvarchar(50) not null unique,
    number int unsigned not null unique check(length(number) <= 5),
    telephone int unsigned unique check(length(telephone) <= 5)
);
insert into rooms (name, number, telephone) values ('Dílna', 001, 2241), ('Ekonomické', 202, 2295), ('Kuchyňka', 102, 2293), ('Ředitelna', 101, 2292), ('Sklad', 002, 2243), ('Šatna', 003, null), ('Toalety', 203, null), ('Xerox', 201, 2296), ('Zasedací místnost', 104, 2294);
-- select * from rooms;

drop table if exists employees;
create table employees (
   id varchar(36) not null primary key default (uuid()),
   first_name nvarchar(30) not null,
   last_name nvarchar(30) not null,
   salary double(12,2) unsigned not null, -- One bilion is maximum salary

   job_title_id varchar(36) not null,
   foreign key (job_title_id) references job_titles(id),

   room_id varchar(36) not null,
   foreign key (room_id) references rooms(id),

   login varchar(30) not null unique,
   password varchar(60) not null,
   is_admin bool not null
);
insert into employees
(first_name, last_name, salary, job_title_id, room_id, login, password, is_admin)
values
    ('Jiřina', 'Hamáčková', 32000.00, (select id from job_titles where name like 'ekonom(ka)'), (select id from rooms where name like 'Ekonomické'), 'J.Hamackova', '', true),
    ('Jindřich', 'Holzer', 22000.00, (select id from job_titles where name like 'techni(k/čka)'), (select id from rooms where name like 'Dílna'), 'J.Holzer', '', false),
    ('Stanislav', 'Janovič', 22000.00, (select id from job_titles where name like 'techni(k/čka)'), (select id from rooms where name like 'Dílna'), 'S.Janovic', '', false),
    ('Tomáš', 'Kalousek', 23000.00, (select id from job_titles where name like 'techni(k/čka)'), (select id from rooms where name like 'Dílna'), 'T.Kalousek', '', true),
    ('Alena', 'Krátká', 24000.00, (select id from job_titles where name like 'techni(k/čka)'), (select id from rooms where name like 'Dílna'), 'A.Kratka', '', false),
    ('Stanislav', 'Lorenc', 14000.00, (select id from job_titles where name like 'skladní(k/ce)'), (select id from rooms where name like 'Sklad'), 'S.Lorenc', '', false),
    ('Martina', 'Marková', 14500.00, (select id from job_titles where name like 'skladní(k/ce)'), (select id from rooms where name like 'Sklad'), 'M.Markova', '', false),
    ('Alena', 'Netěsná', 42000.00, (select id from job_titles where name like 'ekonom(ka)'), (select id from rooms where name like 'Ekonomické'), 'A.Netesna', '', false),
    ('František', 'Netěsný', 65000.00, (select id from job_titles where name like 'ředitel(ka)'), (select id from rooms where name like 'Ředitelna'), 'F.Netesny', '', false),
    ('Milan', 'Steiner', 29000.00, (select id from job_titles where name like 'mistr(yně)'), (select id from rooms where name like 'Dílna'), 'M.Steiner', '', false)
;
-- select * from employees;

drop table if exists active_keys;
create table active_keys (
    employee_id varchar(36) not null,
    foreign key (employee_id) references employees(id),
    room_id varchar(36) not null,
    foreign key (room_id) references rooms(id),
    unique key `employee_id_and_room_id_uniqueness` (`employee_id`, `room_id`) -- one person can at one time hold specific key only once
);
insert into active_keys
    (employee_id, room_id)
    values
    ((select id from employees where first_name like 'Jiřina' and last_name like 'Hamáčková'), (select id from rooms where name like 'Ekonomické')),
    ((select id from employees where first_name like 'Jiřina' and last_name like 'Hamáčková'), (select id from rooms where name like 'Kuchyňka')),
    ((select id from employees where first_name like 'Jiřina' and last_name like 'Hamáčková'), (select id from rooms where name like 'Toalety')),
    ((select id from employees where first_name like 'Jiřina' and last_name like 'Hamáčková'), (select id from rooms where name like 'Xerox')),
    ((select id from employees where first_name like 'Jiřina' and last_name like 'Hamáčková'), (select id from rooms where name like 'Zasedací místnost')),

    ((select id from employees where first_name like 'Jindřich' and last_name like 'Holzer'), (select id from rooms where name like 'Dílna')),
    ((select id from employees where first_name like 'Jindřich' and last_name like 'Holzer'), (select id from rooms where name like 'Šatna')),
    ((select id from employees where first_name like 'Jindřich' and last_name like 'Holzer'), (select id from rooms where name like 'Toalety')),

    ((select id from employees where first_name like 'Stanislav' and last_name like 'Janovič'), (select id from rooms where name like 'Dílna')),
    ((select id from employees where first_name like 'Stanislav' and last_name like 'Janovič'), (select id from rooms where name like 'Šatna')),
    ((select id from employees where first_name like 'Stanislav' and last_name like 'Janovič'), (select id from rooms where name like 'Toalety')),

    ((select id from employees where first_name like 'Tomáš' and last_name like 'Kalousek'), (select id from rooms where name like 'Dílna')),
    ((select id from employees where first_name like 'Tomáš' and last_name like 'Kalousek'), (select id from rooms where name like 'Šatna')),
    ((select id from employees where first_name like 'Tomáš' and last_name like 'Kalousek'), (select id from rooms where name like 'Toalety')),

    ((select id from employees where first_name like 'Alena' and last_name like 'Krátká'), (select id from rooms where name like 'Dílna')),
    ((select id from employees where first_name like 'Alena' and last_name like 'Krátká'), (select id from rooms where name like 'Šatna')),
    ((select id from employees where first_name like 'Alena' and last_name like 'Krátká'), (select id from rooms where name like 'Toalety')),

    ((select id from employees where first_name like 'Stanislav' and last_name like 'Lorenc'), (select id from rooms where name like 'Sklad')),
    ((select id from employees where first_name like 'Stanislav' and last_name like 'Lorenc'), (select id from rooms where name like 'Šatna')),
    ((select id from employees where first_name like 'Stanislav' and last_name like 'Lorenc'), (select id from rooms where name like 'Toalety')),

    ((select id from employees where first_name like 'Martina' and last_name like 'Marková'), (select id from rooms where name like 'Sklad')),
    ((select id from employees where first_name like 'Martina' and last_name like 'Marková'), (select id from rooms where name like 'Šatna')),
    ((select id from employees where first_name like 'Martina' and last_name like 'Marková'), (select id from rooms where name like 'Toalety')),

    ((select id from employees where first_name like 'Alena' and last_name like 'Netěsná'), (select id from rooms where name like 'Ekonomické')),
    ((select id from employees where first_name like 'Alena' and last_name like 'Netěsná'), (select id from rooms where name like 'Kuchyňka')),
    ((select id from employees where first_name like 'Alena' and last_name like 'Netěsná'), (select id from rooms where name like 'Ředitelna')),
    ((select id from employees where first_name like 'Alena' and last_name like 'Netěsná'), (select id from rooms where name like 'Toalety')),
    ((select id from employees where first_name like 'Alena' and last_name like 'Netěsná'), (select id from rooms where name like 'Xerox')),
    ((select id from employees where first_name like 'Alena' and last_name like 'Netěsná'), (select id from rooms where name like 'Zasedací místnost')),

    ((select id from employees where first_name like 'František' and last_name like 'Netěsný'), (select id from rooms where name like 'Dílna')),
    ((select id from employees where first_name like 'František' and last_name like 'Netěsný'), (select id from rooms where name like 'Ekonomické')),
    ((select id from employees where first_name like 'František' and last_name like 'Netěsný'), (select id from rooms where name like 'Kuchyňka')),
    ((select id from employees where first_name like 'František' and last_name like 'Netěsný'), (select id from rooms where name like 'Ředitelna')),
    ((select id from employees where first_name like 'František' and last_name like 'Netěsný'), (select id from rooms where name like 'Sklad')),
    ((select id from employees where first_name like 'František' and last_name like 'Netěsný'), (select id from rooms where name like 'Šatna')),
    ((select id from employees where first_name like 'František' and last_name like 'Netěsný'), (select id from rooms where name like 'Toalety')),
    ((select id from employees where first_name like 'František' and last_name like 'Netěsný'), (select id from rooms where name like 'Xerox')),
    ((select id from employees where first_name like 'František' and last_name like 'Netěsný'), (select id from rooms where name like 'Zasedací místnost')),

    ((select id from employees where first_name like 'Milan' and last_name like 'Steiner'), (select id from rooms where name like 'Dílna')),
    ((select id from employees where first_name like 'Milan' and last_name like 'Steiner'), (select id from rooms where name like 'Kuchyňka')),
    ((select id from employees where first_name like 'Milan' and last_name like 'Steiner'), (select id from rooms where name like 'Šatna')),
    ((select id from employees where first_name like 'Milan' and last_name like 'Steiner'), (select id from rooms where name like 'Toalety')),
    ((select id from employees where first_name like 'Milan' and last_name like 'Steiner'), (select id from rooms where name like 'Xerox')),
    ((select id from employees where first_name like 'Milan' and last_name like 'Steiner'), (select id from rooms where name like 'Zasedací místnost'))
;
-- select * from active_keys;