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

def batchGetContent_1():
    for item in range(0,50000):
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
    for item in range(50000,100000):
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
    for item in range(100000,150000):
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
    for item in range(150000,200000):
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
    for item in range(200000,250000):
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

def batchGetContent_6():
    for item in range(250000,300000):
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

def batchGetContent_7():
    for item in range(300000,350000):
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

def batchGetContent_8():
    for item in range(350000,400000):
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

def batchGetContent_9():
    for item in range(400000,450000):
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

def batchGetContent_10():
    for item in range(450000,500000):
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
    p6 = multiprocessing.Process(target = batchGetContent_6)
    p7 = multiprocessing.Process(target = batchGetContent_7)
    p8 = multiprocessing.Process(target = batchGetContent_8)
    p9 = multiprocessing.Process(target = batchGetContent_9)
    p10 = multiprocessing.Process(target = batchGetContent_10)

    p1.start()
    p2.start()
    p3.start()
    p4.start()
    p5.start()
    p6.start()
    p7.start()
    p8.start()
    p9.start()
    p10.start()


if __name__ == '__main__':
    main()
