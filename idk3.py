import smtplib, os, sys, time

f = open("useremail.txt", "r")
x = f.read()
f.close()

g = open("tasktime.txt", "r")
t = g.read()
g.close()

t = t.strip()
t = t.replace(" ", "")
t = int(t)
t = int(t/1000)

time.sleep(t)

content = 'This is not spam... \n Poopers. Signed, aaron mei'

mail = smtplib.SMTP("smtp.gmail.com", 587)

mail.ehlo

mail.starttls()

mail.login('purpletoasters17@gmail.com', 'Purple Toaster')


mail.sendmail('purpletoasters17@gmail.com', x, content)


mail.close()
