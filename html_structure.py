#!/usr/bin/env python3

import signal
import sys

# pip install beautifulsoup4
from bs4 import BeautifulSoup, NavigableString
from bs4.formatter import XMLFormatter

signal.signal(signal.SIGPIPE, signal.SIG_DFL)

html = sys.stdin.read()

soup = BeautifulSoup(html, "xml")

for node in soup.find_all(string=True):
    if isinstance(node, NavigableString):
        node.extract()

print(
    soup.prettify(
        formatter=XMLFormatter(indent=2)
    )
)

