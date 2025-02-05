-- 1. 200 самых популярных фильмов для указанных жанров (Drama, Thriller и Comedy)
SELECT m.id, m.title, COUNT(r.id) AS rating_count
FROM movies m
JOIN genres_movies gm ON m.id = gm.movie_id
JOIN genres g ON gm.genre_id = g.id
JOIN ratings r ON m.id = r.movie_id
WHERE g.name IN ('Drama', 'Thriller', 'Comedy')
GROUP BY m.id, m.title
ORDER BY rating_count DESC
LIMIT 200;

-- 2. 50 самых популярных фильмов для указанных профессий (Engineer, Programmer и Marketing)
SELECT m.id, m.title, COUNT(r.id) AS rating_count
FROM movies m
JOIN ratings r ON m.id = r.movie_id
JOIN users u ON r.user_id = u.id
JOIN occupations o ON u.occupation_id = o.id
WHERE o.name IN ('Engineer', 'Programmer', 'Marketing')
GROUP BY m.id, m.title
ORDER BY rating_count DESC
LIMIT 50;

-- 3. 200 самых непопулярных фильмов, просмотренных пользователями в возрасте от 18 до 35 лет
SELECT m.id, m.title, COUNT(r.id) AS rating_count
FROM movies m
JOIN ratings r ON m.id = r.movie_id
JOIN users u ON r.user_id = u.id
WHERE u.age BETWEEN 18 AND 35
GROUP BY m.id, m.title
ORDER BY rating_count ASC
LIMIT 200;

--4. 100 фильмов, снятых в указанный период (с 1993 по 1997 год) с максимальной оценкой пользователей женского пола
SELECT m.id, m.title, MAX(r.rating) AS max_rating
FROM movies m
JOIN ratings r ON m.id = r.movie_id
JOIN users u ON r.user_id = u.id
WHERE m.release_date BETWEEN '1993-01-01' AND '1997-12-31'
  AND u.gender = 'F'
GROUP BY m.id, m.title
ORDER BY max_rating DESC
LIMIT 100;