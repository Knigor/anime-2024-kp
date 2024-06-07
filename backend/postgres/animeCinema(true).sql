/*==============================================================*/
/* DBMS name:      PostgreSQL 9.x                               */
/* Created on:     11.12.2023 17:57:37                          */
/*==============================================================*/


drop index Anime_PK;

drop table Anime;

drop index Name_anime;

drop index viewed_FK;

drop index write_FK;

drop index View_PK;

drop table View;

drop index genre_PK;

drop table genre;

drop index profile_PK;

drop table profile;

drop index stores_rating_FK;

drop index puts_rating_FK;

drop index rating_anime_PK;

drop table rating_anime;

drop index stores_review_FK;

drop index write_review_FK;

drop index review_PK;

drop table review;

drop index stores_genre_FK;

drop index anime_stores_genre_FK;

drop index stores_genre_PK;

drop table stores_genre;

drop index registration_FK;

drop index user_PK;

drop table "user";

/*==============================================================*/
/* Table: Anime                                                 */
/*==============================================================*/
create table Anime (
   title_anime          VARCHAR(50)          not null,
   year_release         INT4                 not null,
   director             VARCHAR(50)          not null,
   studio_manufacture   VARCHAR(40)          not null,
   discription_plot     VARCHAR(250)         not null,
   constraint PK_ANIME primary key (title_anime, year_release)
);

/*==============================================================*/
/* Index: Anime_PK                                              */
/*==============================================================*/
create unique index Anime_PK on Anime (
title_anime,
year_release
);

/*==============================================================*/
/* Table: View                                                  */
/*==============================================================*/
create table View (
   ID_view              SERIAL               not null,
   title_anime          VARCHAR(50)          null,
   year_release         INT4                 null,
   email                VARCHAR(100)         null,
   favourites           VARCHAR(120)         not null,
   history              VARCHAR(120)         not null,
   date_view            DATE                 null,
   constraint PK_VIEW primary key (ID_view)
);

/*==============================================================*/
/* Index: View_PK                                               */
/*==============================================================*/
create unique index View_PK on View (
ID_view
);

/*==============================================================*/
/* Index: write_FK                                              */
/*==============================================================*/
create  index write_FK on View (
title_anime,
year_release
);

/*==============================================================*/
/* Index: viewed_FK                                             */
/*==============================================================*/
create  index viewed_FK on View (
email
);

/*==============================================================*/
/* Index: Name_anime                                            */
/*==============================================================*/
create  index Name_anime on View (
title_anime
);

/*==============================================================*/
/* Table: genre                                                 */
/*==============================================================*/
create table genre (
   id_genre             SERIAL               not null,
   name_genre           VARCHAR(30)          not null,
   constraint PK_GENRE primary key (id_genre)
);

/*==============================================================*/
/* Index: genre_PK                                              */
/*==============================================================*/
create unique index genre_PK on genre (
id_genre
);

/*==============================================================*/
/* Table: profile                                               */
/*==============================================================*/
create table profile (
   id_profile           SERIAL               not null,
   avatar               CHAR(254)            null,
   achievements         CHAR(254)            null,
   constraint PK_PROFILE primary key (id_profile)
);

/*==============================================================*/
/* Index: profile_PK                                            */
/*==============================================================*/
create unique index profile_PK on profile (
id_profile
);

/*==============================================================*/
/* Table: rating_anime                                          */
/*==============================================================*/
create table rating_anime (
   id_rating            SERIAL               not null,
   email                VARCHAR(100)         null,
   title_anime          VARCHAR(50)          not null,
   year_release         INT4                 not null,
   rating               INT4                 not null,
   date_rating          DATE                 null,
   constraint PK_RATING_ANIME primary key (id_rating)
);

/*==============================================================*/
/* Index: rating_anime_PK                                       */
/*==============================================================*/
create unique index rating_anime_PK on rating_anime (
id_rating
);

/*==============================================================*/
/* Index: puts_rating_FK                                        */
/*==============================================================*/
create  index puts_rating_FK on rating_anime (
email
);

/*==============================================================*/
/* Index: stores_rating_FK                                      */
/*==============================================================*/
create  index stores_rating_FK on rating_anime (
title_anime,
year_release
);

/*==============================================================*/
/* Table: review                                                */
/*==============================================================*/
create table review (
   id_review            SERIAL               not null,
   email                VARCHAR(100)         not null,
   title_anime          VARCHAR(50)          not null,
   year_release         INT4                 not null,
   text_review          VARCHAR(254)         not null,
   success_moderation   BOOL                 not null,
   date_review          DATE                 null,
   constraint PK_REVIEW primary key (id_review)
);

/*==============================================================*/
/* Index: review_PK                                             */
/*==============================================================*/
create unique index review_PK on review (
id_review
);

/*==============================================================*/
/* Index: write_review_FK                                       */
/*==============================================================*/
create  index write_review_FK on review (
email
);

/*==============================================================*/
/* Index: stores_review_FK                                      */
/*==============================================================*/
create  index stores_review_FK on review (
title_anime,
year_release
);

/*==============================================================*/
/* Table: stores_genre                                          */
/*==============================================================*/
create table stores_genre (
   id_genre             INT4                 not null,
   title_anime          VARCHAR(50)          not null,
   year_release         INT4                 not null,
   constraint PK_STORES_GENRE primary key (id_genre, title_anime, year_release)
);

/*==============================================================*/
/* Index: stores_genre_PK                                       */
/*==============================================================*/
create unique index stores_genre_PK on stores_genre (
id_genre,
title_anime,
year_release
);

/*==============================================================*/
/* Index: anime_stores_genre_FK                                 */
/*==============================================================*/
create  index anime_stores_genre_FK on stores_genre (
title_anime,
year_release
);

/*==============================================================*/
/* Index: stores_genre_FK                                       */
/*==============================================================*/
create  index stores_genre_FK on stores_genre (
id_genre
);

/*==============================================================*/
/* Table: "user"                                                */
/*==============================================================*/
create table "user" (
   email                VARCHAR(100)         not null,
   id_profile           INT4                 null,
   name_user            VARCHAR(16)          not null,
   hash_password        VARCHAR(64)          not null,
   date_registration    DATE                 null,
   type_user            VARCHAR(30)          not null,
   constraint PK_USER primary key (email)
);

/*==============================================================*/
/* Index: user_PK                                               */
/*==============================================================*/
create unique index user_PK on "user" (
email
);

/*==============================================================*/
/* Index: registration_FK                                       */
/*==============================================================*/
create  index registration_FK on "user" (
id_profile
);

alter table View
   add constraint FK_VIEW_VIEWED_USER foreign key (email)
      references "user" (email)
      on delete restrict on update restrict;

alter table View
   add constraint FK_VIEW_WRITE_ANIME foreign key (title_anime, year_release)
      references Anime (title_anime, year_release)
      on delete restrict on update restrict;

alter table rating_anime
   add constraint FK_RATING_A_PUTS_RATI_USER foreign key (email)
      references "user" (email)
      on delete restrict on update restrict;

alter table rating_anime
   add constraint FK_RATING_A_STORES_RA_ANIME foreign key (title_anime, year_release)
      references Anime (title_anime, year_release)
      on delete restrict on update restrict;

alter table review
   add constraint FK_REVIEW_STORES_RE_ANIME foreign key (title_anime, year_release)
      references Anime (title_anime, year_release)
      on delete restrict on update restrict;

alter table review
   add constraint FK_REVIEW_WRITE_REV_USER foreign key (email)
      references "user" (email)
      on delete restrict on update restrict;

alter table stores_genre
   add constraint FK_STORES_G_ANIME_STO_ANIME foreign key (title_anime, year_release)
      references Anime (title_anime, year_release)
      on delete restrict on update restrict;

alter table stores_genre
   add constraint FK_STORES_G_STORES_GE_GENRE foreign key (id_genre)
      references genre (id_genre)
      on delete restrict on update restrict;

alter table "user"
   add constraint FK_USER_REGISTRAT_PROFILE foreign key (id_profile)
      references profile (id_profile)
      on delete restrict on update restrict;

