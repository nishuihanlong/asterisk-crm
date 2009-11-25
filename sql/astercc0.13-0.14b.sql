alter table historycdr add note text default '';
alter table mycdr add note text default '';
alter table historycdr add setfreecall enum('yes','no') default 'no';
alter table mycdr add setfreecall enum('yes','no') default 'no';

