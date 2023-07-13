from django.db import models

# Create your models here.

class Dht11(models.Model):
    temperature = models.IntegerField()
    humidity = models.IntegerField()
    datetime = models.DateTimeField()

    class Meta:
        managed = False
        db_table = 'dht11'