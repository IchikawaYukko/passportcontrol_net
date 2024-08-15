FROM	debian:stable-20240812-slim

RUN	apt-get update && apt-get install -y cron time curl && \
	echo '0 0 * * * root /download_border.sh >> /download.log' >> /etc/crontab
COPY	download_border.sh /download_border.sh

WORKDIR		/var/www/html
ENTRYPOINT	["cron", "-f"]
