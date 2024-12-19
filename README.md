-- Migrations will appear here as you chat with AI

create table users (
  id bigint primary key generated always as identity,
  name text not null,
  email text unique not null,
  password text not null,
  created_at timestamp with time zone default now()
);

create table books (
  id bigint primary key generated always as identity,
  title text not null,
  author text not null,
  isbn text unique not null,
  published_date date,
  available boolean default true
);

create table rentals (
  id bigint primary key generated always as identity,
  user_id bigint references users (id),
  book_id bigint references books (id),
  rented_at timestamp with time zone default now(),
  due_date timestamp with time zone,
  returned_at timestamp with time zone
);

create index idx_rentals_user_id on rentals using btree (user_id);

create index idx_rentals_book_id on rentals using btree (book_id);

create index idx_books_available on books using btree (available);

create table reviews (
  id bigint primary key generated always as identity,
  user_id bigint references users (id),
  book_id bigint references books (id),
  rating int check (
    rating >= 1
    and rating <= 5
  ),
  comment text,
  created_at timestamp with time zone default now()
);

create index idx_reviews_user_id on reviews using btree (user_id);

create index idx_reviews_book_id on reviews using btree (book_id);

create table collections (
  id bigint primary key generated always as identity,
  name text not null,
  description text,
  created_at timestamp with time zone default now()
);

create table book_collections (
  id bigint primary key generated always as identity,
  book_id bigint references books (id),
  collection_id bigint references collections (id)
);

create index idx_book_collections_book_id on book_collections using btree (book_id);

create index idx_book_collections_collection_id on book_collections using btree (collection_id);

create table favorites (
  id bigint primary key generated always as identity,
  user_id bigint references users (id),
  book_id bigint references books (id),
  created_at timestamp with time zone default now()
);

create index idx_favorites_user_id on favorites using btree (user_id);

create index idx_favorites_book_id on favorites using btree (book_id);