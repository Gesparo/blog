# Зaдaниe нa знaниe SQL:

### Входные данные

```postgresql
create temp table users(id bigserial, group_id bigint);
insert into users(group_id) values (1), (1), (1), (2), (1), (3);
```

### Запрос

```postgresql
SELECT  MIN(id) as min_id, group_id, count(id)
FROM    (
          SELECT  *,
            ROW_NUMBER() OVER (PARTITION BY group_id ORDER BY id) AS rno,
            ROW_NUMBER() OVER (ORDER BY id) AS rne
          FROM users_t
        ) q
GROUP BY
  group_id, rne - rno
ORDER BY
  MIN(id)
```

### Выходные данные

min_id | group_id | count
------------ | ------------- | -------------
1 | 1 | 3
4 | 2 | 1
5 | 1 | 1
6 | 3 | 1
