-- D1: Top 10 most active users in the last 7 days by total interactions.
SELECT u.id, u.email, COUNT(i.id) AS total_interactions
FROM users u
JOIN interactions i ON i.user_id = u.id
WHERE i.created_at >= NOW() - INTERVAL '7 days'
GROUP BY u.id, u.email
ORDER BY total_interactions DESC
LIMIT 10;

-- D2: For a given user_id, return posts from the users they interact with most in the last 30 days.
WITH ranked_contacts AS (
    SELECT i2.user_id AS contact_user_id, COUNT(*) AS interaction_count
    FROM interactions i2
    WHERE i2.created_at >= NOW() - INTERVAL '30 days'
      AND i2.post_id IN (
          SELECT id FROM posts WHERE user_id = :user_id
      )
    GROUP BY i2.user_id
    ORDER BY interaction_count DESC
)
SELECT p.id AS post_id, p.user_id AS author_id, p.text, p.created_at
FROM posts p
JOIN ranked_contacts rc ON rc.contact_user_id = p.user_id
WHERE p.created_at >= NOW() - INTERVAL '30 days'
ORDER BY rc.interaction_count DESC, p.created_at DESC;

-- D3: Posts viewed more than 100 times but with zero reactions.
SELECT p.id AS post_id, p.user_id AS author_id,
       COUNT(CASE WHEN i.type = 'view' THEN 1 END) AS view_count,
       p.created_at
FROM posts p
LEFT JOIN interactions i ON i.post_id = p.id
GROUP BY p.id, p.user_id, p.created_at
HAVING COUNT(CASE WHEN i.type = 'view' THEN 1 END) > 100
   AND COUNT(CASE WHEN i.type = 'reaction' THEN 1 END) = 0;

-- D4: Users who created more than 20 posts in the last 24 hours.
SELECT u.email, COUNT(p.id) AS post_count
FROM users u
JOIN posts p ON p.user_id = u.id
WHERE p.created_at >= NOW() - INTERVAL '24 hours'
GROUP BY u.id, u.email
HAVING COUNT(p.id) > 20;
