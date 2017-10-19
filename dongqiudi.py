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


def get_li(doc, filename):
    soup = BeautifulSoup(doc, 'html.parser')
    title = soup.find('title')
    title_body = title.get_text()
    write_result(title_body, filename)
    ol = soup.find('div', attrs={'class':'detail'})
    for i in ol.find_all('p'):
        str = i.get_text()
        print(str)
        write_result(str, filename)

def write_result(str,filename):
    f = open(filename,'a')
    f.write(str)
    f.write("\n")

def batchGetContent():
    for item in range(0,999999):
        try:
            originalurl = 'https://www.dongqiudi.com/archive/' + str(item) + '.html'
            doc = download_page(originalurl)
            filename = 'football/' + str(item) + '.txt'
            print(filename)
            get_li(doc,filename)
        except Exception as err:
            print(err)
            print(str(item) + str(' web site cannot be spidered! Sorry!'))
            continue


def main():
    result = batchGetContent()



if __name__ == '__main__':
    main()
