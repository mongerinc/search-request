FROM ubuntu:16.04

# fix the bash
RUN ln -snf /bin/bash /bin/sh

# Install packages
RUN apt-get update && apt-get install -my \
  vim \
  git \
  curl \
  wget \
  zip \
  bzip2 \
  php7.0-cli

# Remove unused packages
RUN apt-get autoremove -y

# Add some color to vim
RUN printf "color desert" > /root/.vimrc

# Get PhantomJS
WORKDIR /usr/local/share
RUN wget https://bitbucket.org/ariya/phantomjs/downloads/phantomjs-2.1.1-linux-x86_64.tar.bz2
RUN tar xjf phantomjs-2.1.1-linux-x86_64.tar.bz2
RUN ln -s /usr/local/share/phantomjs-2.1.1-linux-x86_64/bin/phantomjs /usr/local/share/phantomjs
RUN ln -s /usr/local/share/phantomjs-2.1.1-linux-x86_64/bin/phantomjs /usr/local/bin/phantomjs
RUN ln -s /usr/local/share/phantomjs-2.1.1-linux-x86_64/bin/phantomjs /usr/bin/phantomjs
WORKDIR /

# Add the code to the container
RUN mkdir /search-request
COPY . /search-request

# Move the start file
COPY  start.sh /bin/original_start.sh
RUN tr -d '\r' < /bin/original_start.sh > /bin/start.sh
RUN chmod -R 700 /bin/start.sh

EXPOSE 9000

ENV TERM xterm

CMD ["sh", "/bin/start.sh"]