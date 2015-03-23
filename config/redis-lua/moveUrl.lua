local links = redis.call("ZRANGEBYSCORE", KEYS[1], 0, ARGV[1], 'LIMIT', 0, 1000)
redis.call("ZREMRANGEBYSCORE", KEYS[1], 0, ARGV[1])
redis.call("LPUSH", KEYS[2], unpack(links))
return links