create table ripple (
   num int not null auto_increment,
   parent int not null,
   id char(20) not null,
   name  char(20) not null,
   content text not null,
   regist_day char(20),
   primary key(num)
);

ALTER TABLE ripple
ADD COLUMN public_id varchar(20) not null;

-- _mem 테이블에 있는 public_id 필드 데이터를 옮기기
UPDATE ripple r
JOIN _mem m ON r.id = m.id
SET r.public_id = m.public_id
WHERE r.public_id IS NULL OR r.public_id = '';