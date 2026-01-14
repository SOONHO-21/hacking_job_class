create table board (
    num int not null auto_increment,
    id char(20) not null,
    name char(20) not null,
    public_id varchar(20) not null,
    subject char(200) not null,
    content text,
    is_html char(1),
    regist_day char(20),
    file_name char(40),
    file_type char(40),
    file_copied char(40),
    primary key(num)
);

ALTER TABLE board
ADD COLUMN public_id varchar(20) not null;

-- _mem 테이블에 있는 public_id 필드 데이터를 옮기기
UPDATE board b
JOIN _mem m ON b.id = m.id
SET b.public_id = m.public_id
WHERE b.public_id IS NULL OR b.public_id = '';