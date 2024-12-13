import requests
from bs4 import BeautifulSoup
url = 'https://www.cs.mcgill.ca/academic/courses/'

page = requests.get(url)

soup = BeautifulSoup(page.content, 'html.parser')
courses = soup.find(id='courses')

courses_a = courses.find_all('a',class_='list-group-item')

def checkAvailable(a_tag):
    return a_tag.find('span') is None or a_tag.find('span').text != 'Unavailable'

def getCourseName(a_tag):
    return str(a_tag).split('>')[1].split('<')[0].strip('\n\t ')

def getCourseTagandID(course_name):
    return course_name[0:4], course_name[5:].split(' ')[0]

for a_tag in courses_a:
    available = checkAvailable(a_tag)
    course_name = getCourseName(a_tag)
    print(course_name)
    print(getCourseTagandID(course_name))
    print(available)
    
