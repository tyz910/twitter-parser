twitter-parser
==============

Установка
-----------------------

Клонируем репозиторий

	git clone https://github.com/tyz910/twitter-parser.git
	cd twitter-parser

Прописываем настройки подключения к базе и токен. [Подробнее о конфигурации подключения к базе](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html) 

	mv config.yml.dist config.yml
	vi config.yml

Устанавливаем зависимости

	composer install

Накатываем миграции

	./console migrations:migrate

Можно пользоваться:

	./console
или

	php console

Использование
-----------------------
Получение id пользователя по имени

	./console twitter:user:find lentaruofficial
	lentaruofficial - 228661749

Загрузка статистики по пользователю

	./console twitter:user:load_stat 228661749
	Load stat for user #228661749.
	+--------+------------+-----------+------------+---------------------+
	| tweets | followings | followers | parse_time | lastparse           |
	+--------+------------+-----------+------------+---------------------+
	| 107423 | 179        | 150994    | 682        | 2013-10-15 15:33:50 |
	+--------+------------+-----------+------------+---------------------+

Загрузка твитов пользователя

	./console twitter:user:load_tweets 228661749
	Search tweets for user 228661749 - 2 found

Твиты ищутся в две стороны

	./console twitter:user:load_tweets 228661749 --dir=new
	./console twitter:user:load_tweets 228661749 --dir=old
	
Ставим в крон и следим за лентой

	0 * * * * /home/user/twitter-parser/console load_stat 228661749
	5 * * * * /home/user/twitter-parser/console load_tweets 228661749
