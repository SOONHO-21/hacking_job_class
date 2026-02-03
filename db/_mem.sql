create table _mem (
    num int not null auto_increment,
    id char(20) not null UNIQUE,
    pass varchar(100) not null,
    name char(20) not null,
    public_id varchar(20) not null UNIQUE,
    email char(200),
    regist_day char(20),
    level int not null default 1,  -- 1=유저, 9=관리자
    profile_img VARCHAR(255) DEFAULT NULL,
    primary key(num)
);

ALTER TABLE _mem 
ADD UNIQUE (id),
ADD UNIQUE (public_id);