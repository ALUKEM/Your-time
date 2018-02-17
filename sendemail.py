#!/usr/bin/env python
import sys

x = sys.argv[1].strip("'")

import smtplib

content = 'Thank you for creating an account'

mail = smtplib.SMTP("smtp.gmail.com", 587)

mail.ehlo

mail.starttls()

mail.login('purpletoasters17@gmail.com', 'Purple Toaster')


mail.sendmail('purpletoasters17@gmail.com', x, content)


mail.close()

