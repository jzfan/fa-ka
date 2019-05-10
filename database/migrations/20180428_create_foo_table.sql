create table foo (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `bar` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    PRIMARY KEY (`id`)
);

insert into foo (`bar`) values ('Seeing');
insert into foo (`bar`) values ('this');
insert into foo (`bar`) values ('means');
insert into foo (`bar`) values ('the');
insert into foo (`bar`) values ('migrations');
insert into foo (`bar`) values ('success.');


