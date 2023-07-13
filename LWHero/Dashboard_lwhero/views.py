from django.shortcuts import render
from .models import Dht11

# Create your views here.

def index(request):
    Dht = Dht11.objects.all()
    context = {
        'Dht': Dht
    }
    return render(request, 'index.html', context)