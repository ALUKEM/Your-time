#!/usr/bin/env python
import sys, os
x = sys.argv[1].replace("'", "")
y = sys.argv[2].replace("'", "")

f = open("useremail.txt", "w")
f.write(x)
f.close()

g = open("tasktime.txt", "w")
g.write(y)
g.close()

os.system("idk3.py")
exit()


