FROM cypress/included:4.1.0

# 日本語化
RUN apt-get update
RUN apt-get install --no-install-recommends -y fonts-noto fonts-noto-cjk

# 追加ライブラリ
RUN npm install dayjs mysql