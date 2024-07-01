/*==============================================================*/
/* Table: Clients */
/*==============================================================*/
create table Clients (
cln_id SERIAL not null,
cln_name VARCHAR(32) not null,
cln_surname VARCHAR(32) not null,
cln_patronymic VARCHAR(32) null,
cln_phone_number CHAR(10) not null,
constraint PK_CLIENTS primary key (cln_id)
);
/*==============================================================*/
/* Index: Clients_PK */
/*==============================================================*/
create unique index Clients_PK on Clients (
cln_id
);
/*==============================================================*/
/* Table: Feedback */
/*==============================================================*/
create table Feedback (
rvw_id SERIAL not null,
cln_id INT4 not null,
rvw_date DATE not null,
review VARCHAR(1024) not null,
rvw_time TIME not null,
constraint PK_FEEDBACK primary key (rvw_id)
);
/*==============================================================*/
/* Index: Feedback_PK */
/*==============================================================*/
create unique index Feedback_PK on Feedback (

rvw_id
);
/*==============================================================*/
/* Index: "Write a review_FK" */
/*==============================================================*/
create index "Write a review_FK" on Feedback (
cln_id
);
/*==============================================================*/
/* Table: Haircut */
/*==============================================================*/
create table Haircut (
hrt_id SERIAL not null,
hrt_name VARCHAR(32) not null,
hrt_price MONEY not null,
constraint PK_HAIRCUT primary key (hrt_id)
);
/*==============================================================*/
/* Index: Haircut_PK */
/*==============================================================*/
create unique index Haircut_PK on Haircut (
hrt_id
);
/*==============================================================*/
/* Table: Haircut_appointments */
/*==============================================================*/
create table Haircut_appointments (
app_id SERIAL not null,
mng_id INT4 null,
hrt_id INT4 not null,
cln_id INT4 not null,
sch_id INT4 not null,
app_status VARCHAR(32) not null,
app_time TIME not null,
app_data DATE not null,
constraint PK_HAIRCUT_APPOINTMENTS primary key (app_id)
);
/*==============================================================*/
/* Index: Haircut_appointments_PK */
/*==============================================================*/
create unique index Haircut_appointments_PK on Haircut_appointments (
app_id
);
/*==============================================================*/

/* Index: Confirm_FK */
/*==============================================================*/
create index Confirm_FK on Haircut_appointments (
mng_id
);
/*==============================================================*/
/* Index: Choose_FK */
/*==============================================================*/
create index Choose_FK on Haircut_appointments (
hrt_id
);
/*==============================================================*/
/* Index: "Sign up and pay_FK" */
/*==============================================================*/
create index "Sign up and pay_FK" on Haircut_appointments (
cln_id
);
/*==============================================================*/
/* Index: "Check entries_FK" */
/*==============================================================*/
create index "Check entries_FK" on Haircut_appointments (
sch_id
);
/*==============================================================*/
/* Table: Hairdressers */
/*==============================================================*/
create table Hairdressers (
hrd_id SERIAL not null,
hrd_name VARCHAR(32) not null,
hrd_surname VARCHAR(32) not null,
hrd_patronymic VARCHAR(32) null,
hrd_phone_number CHAR(10) not null,
constraint PK_HAIRDRESSERS primary key (hrd_id)
);
/*==============================================================*/
/* Index: Hairdressers_PK */
/*==============================================================*/
create unique index Hairdressers_PK on Hairdressers (
hrd_id
);
/*==============================================================*/
/* Table: Managers */
/*==============================================================*/
create table Managers (

mng_id SERIAL not null,
mng_name VARCHAR(32) not null,
mng_surname VARCHAR(32) not null,
mng_patronymic VARCHAR(32) null,
mng_phone_number CHAR(10) not null,
constraint PK_MANAGERS primary key (mng_id)
);
/*==============================================================*/
/* Index: Managers_PK */
/*==============================================================*/
create unique index Managers_PK on Managers (
mng_id
);
/*==============================================================*/
/* Table: Schedule */
/*==============================================================*/
create table Schedule (
sch_id SERIAL not null,
hrd_id INT4 not null,
time_from TIME not null,
time_by TIME not null,
date DATE not null,
constraint PK_SCHEDULE primary key (sch_id)
);
/*==============================================================*/
/* Index: Schedule_PK */
/*==============================================================*/
create unique index Schedule_PK on Schedule (
sch_id
);
/*==============================================================*/
/* Index: "Check work schedule_FK" */
/*==============================================================*/
create index "Check work schedule_FK" on Schedule (
hrd_id
);
alter table Feedback
add constraint "FK_FEEDBACK_WRITE A R_CLIENTS" foreign key (cln_id)
references Clients (cln_id)
on delete restrict on update restrict;
alter table Haircut_appointments
add constraint "FK_HAIRCUT__CHECK ENT_SCHEDULE" foreign key (sch_id)
references Schedule (sch_id)
on delete restrict on update restrict;

alter table Haircut_appointments
add constraint FK_HAIRCUT__CHOOSE_HAIRCUT foreign key (hrt_id)
references Haircut (hrt_id)
on delete restrict on update restrict;
alter table Haircut_appointments
add constraint FK_HAIRCUT__CONFIRM_MANAGERS foreign key (mng_id)
references Managers (mng_id)
on delete restrict on update restrict;
alter table Haircut_appointments
add constraint "FK_HAIRCUT__SIGN UP A_CLIENTS" foreign key (cln_id)
references Clients (cln_id)
on delete restrict on update restrict;
alter table Schedule
add constraint "FK_SCHEDULE_CHECK WOR_HAIRDRES" foreign key (hrd_id)
references Hairdressers (hrd_id)
on delete restrict on update restrict;

/* 
   Вывести заказ и стрижка, которая делалась
   Выбираем идентификатор стрижки, идентификатор записи на стрижку,
   дату и время записи на стрижку.
*/
SELECT haircut.hrt_id, haircut_appointments.app_id, 
       haircut_appointments.app_date, haircut_appointments.app_time
FROM haircut
LEFT JOIN haircut_appointments ON haircut.hrt_id = haircut_appointments.hrt_id
ORDER BY haircut.hrt_id, haircut_appointments.app_date, haircut_appointments.app_time;

/* 
   Вывести количество раз, когда менеджер одобрял ту или иную стрижку.
   Выбираем фамилию менеджера, название стрижки и считаем количество записей,
   группируем по фамилии менеджера и названию стрижки, сортируем по фамилии менеджера и названию стрижки.
*/
SELECT managers.mng_surname as surname, haircut.hrt_name, COUNT(*) as appointments_count
FROM haircut_appointments
JOIN managers ON haircut_appointments.mng_id = managers.mng_id
JOIN haircut ON haircut_appointments.hrt_id = haircut.hrt_id
GROUP BY ROLLUP(managers.mng_surname, haircut.hrt_name)
ORDER BY managers.mng_surname, haircut.hrt_name;

/* 
   Изменение данных клиента с определенным идентификатором.
   Обновляем имя, фамилию, отчество и номер телефона клиента,
   у которого идентификатор cln_id равен указанному значению (замените <47> на конкретный идентификатор).
*/
UPDATE Clients
SET cln_name = 'Даня',
    cln_surname = 'Пупкин',
    cln_patronymic = 'Пупкович',
    cln_phone_number = '+79456456445'
WHERE cln_id = <47>;

/* 
   Удаление записей из таблицы "Клиенты", которые зарегистрированы,
   но не посещали стрижки и не оставляли отзывы, при этом фамилия начинается на 'К'.
   Удаляем из таблицы Clients те записи, для которых не существует связанных записей в таблице feedback
   и haircut_appointments, а также фамилия начинается с буквы 'К'.
*/
DELETE FROM clients
WHERE cln_surname LIKE 'К%' AND NOT EXISTS (
    SELECT 1
    FROM feedback
    WHERE feedback.cln_id = clients.cln_id
) AND NOT EXISTS (
    SELECT 1
    FROM haircut_appointments
    WHERE haircut_appointments.cln_id = clients.cln_id
);

