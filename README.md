twitter-parser
==============

Задание
-----------------------

Нужно написать скрипт который при регулярном запуске должен обрабатывать информацию о твиттах с аккаунта https://twitter.com/lentaruofficial
Необходимо сохранять информацию об аккаунте в две таблицы:

twitterLenta — информация о группе в последний момент времени

	id — id аккаунта
	tweets— количество твитов
	followings — количество читателей (following — подписчики аккаунта)
	followers — показатель "Читает" (follower — количество подписанных аккаунтов этим аккаунтом)
	lastparse — время последнего парсинга
	parse_time — длительность парсинга аккаунта


twitterLenta_history — информация о группе по рпедыдущим моментам парсера

	id — id аккаунта 
	date — дата и время когда был произведен парсинг
	tweets— количество твитов в тот момент времени
	followings — количество читателей (following — подписчики аккаунта) в тот момент времени
	followers — показатель "Читает" (follower — количество подписанных аккаунтов этим аккаунтом) в тот момент времени
	parse_time — длительность парсинга в тот момент времени


twitterLenta_tweets — твиты аккаунта

	id — id твита
	user_id — id аккаунта пользователя
	text — текст твита
	retweet_count — количество ретвитов
	created_at — дата создания


Скрипт должен работать используя Twitter API. 
Можно использовать любые язык программирования и применяемые технологии.

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
