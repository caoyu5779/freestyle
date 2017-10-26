#!/usr/bin/env python
# encoding=utf-8
import requests
import re
import codecs
import multiprocessing

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
        write_result(str, filename)

def write_result(str,filename):
    f = open(filename,'a')
    f.write(str)
    f.write("\n")

class ContentProcess(multiprocessing.Process):
    def __init__(self, interval):
        multiprocessing.Process.__init__(self)
        self.interval = interval

def batchGetContent_1():
    for item in range(0,100000):
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

def batchGetContent_2():
    for item in range(100000,200000):
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

def batchGetContent_3():
    for item in range(200000,300000):
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

def batchGetContent_4():
    for item in range(300000,400000):
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

def batchGetContent_5():
    for item in range(400000,500000):
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
    p1 = multiprocessing.Process(target = batchGetContent_1)
    p2 = multiprocessing.Process(target = batchGetContent_2)
    p3 = multiprocessing.Process(target = batchGetContent_3)
    p4 = multiprocessing.Process(target = batchGetContent_4)
    p5 = multiprocessing.Process(target = batchGetContent_5)

    p1.start()
    p2.start()
    p3.start()
    p4.start()
    p5.start()


if __name__ == '__main__':
    main()
