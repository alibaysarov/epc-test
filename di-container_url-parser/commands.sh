docker run -d \
  --name php-app \
  -v $(pwd)/src:/var/www \
  -p 9000:9000 \
  di-container