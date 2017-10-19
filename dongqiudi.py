#!/usr/bin/env python
# encoding=utf-8
import requests
import re
import codecs
from bs4 import BeautifulSoup

dest_filename = 'tbody.txt'

DOWNLOAD_URL = 'https://www.dongqiudi.com/archive/446252.html'


def download_page(url):
    """获取url地址页面内容"""
    headers = {
        'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.80 Safari/537.36'
    }
    data = requests.get(url, headers=headers).content
    return data


def get_li(doc):
    soup = BeautifulSoup(doc, 'html.parser')
    title = soup.find('title')
    title_body = title.get_text()
    write_result(title_body)
    ol = soup.find('div', attrs={'class':'detail'})
    name = []  # 名字
    star_con = []  # 评价人数
    score = []  # 评分
    detail_list = []  # 短评
    for i in ol.find_all('p'):
        str = i.get_text()
        write_result(str)

def write_result(str):
    f = open('tbody.txt','a')
    f.write(str)
    f.write("\n")


def main():
    url = DOWNLOAD_URL
    detail_arr = []
    title_body_arr = []

    doc = download_page(url)
    detail = get_li(doc)



if __name__ == '__main__':
    main()
