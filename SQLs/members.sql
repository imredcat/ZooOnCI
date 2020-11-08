
drop table if EXISTS members;

create table members(
  mem_pk    int (11)      primary key auto_increment,
  mem_id    varchar (40)             comment '아이디',
  mem_pwd   varchar (64)             comment '비밀번호',
  mem_nm    varchar (30)             comment '회원 이름',
  mem_nick  varchar (20)             comment '닉네임',
  
)