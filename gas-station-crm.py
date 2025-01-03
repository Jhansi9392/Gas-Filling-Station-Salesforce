# app.py
from flask import Flask, render_template, request, redirect, url_for, flash
from flask_sqlalchemy import SQLAlchemy
from flask_login import LoginManager, UserMixin, login_user, login_required, logout_user, current_user
from datetime import datetime
import os

app = Flask(__name__)
app.config['SECRET_KEY'] = 'your_secret_key_here'
app.config['SQLALCHEMY_DATABASE_URI'] = 'sqlite:///gas_station.db'
db = SQLAlchemy(app)
login_manager = LoginManager(app)
login_manager.login_view = 'login'

# Models
class User(UserMixin, db.Model):
    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(80), unique=True, nullable=False)
    password = db.Column(db.String(120), nullable=False)
    is_admin = db.Column(db.Boolean, default=False)

class FuelType(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(50), nullable=False)
    price_per_liter = db.Column(db.Float, nullable=False)
    stock_level = db.Column(db.Float, nullable=False)

class Transaction(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    fuel_type_id = db.Column(db.Integer, db.ForeignKey('fuel_type.id'), nullable=False)
    quantity = db.Column(db.Float, nullable=False)
    total_amount = db.Column(db.Float, nullable=False)
    timestamp = db.Column(db.DateTime, default=datetime.utcnow)
    customer_vehicle = db.Column(db.String(50))

@login_manager.user_loader
def load_user(user_id):
    return User.query.get(int(user_id))

# Routes
@app.route('/')
@login_required
def dashboard():
    fuels = FuelType.query.all()
    transactions = Transaction.query.order_by(Transaction.timestamp.desc()).limit(10)
    return render_template('dashboard.html', fuels=fuels, transactions=transactions)

@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        username = request.form.get('username')
        password = request.form.get('password')
        user = User.query.filter_by(username=username).first()
        
        if user and user.password == password:  # In production, use proper password hashing
            login_user(user)
            return redirect(url_for('dashboard'))
        flash('Invalid credentials')
    return render_template('login.html')

@app.route('/add_transaction', methods=['POST'])
@login_required
def add_transaction():
    fuel_type_id = request.form.get('fuel_type')
    quantity = float(request.form.get('quantity'))
    vehicle = request.form.get('vehicle')
    
    fuel = FuelType.query.get(fuel_type_id)
    if fuel and fuel.stock_level >= quantity:
        total_amount = quantity * fuel.price_per_liter
        transaction = Transaction(
            fuel_type_id=fuel_type_id,
            quantity=quantity,
            total_amount=total_amount,
            customer_vehicle=vehicle
        )
        fuel.stock_level -= quantity
        
        db.session.add(transaction)
        db.session.commit()
        flash('Transaction added successfully')
    else:
        flash('Insufficient stock')
    return redirect(url_for('dashboard'))

@app.route('/update_fuel', methods=['POST'])
@login_required
def update_fuel():
    if not current_user.is_admin:
        flash('Admin access required')
        return redirect(url_for('dashboard'))
        
    fuel_id = request.form.get('fuel_id')
    new_price = request.form.get('price')
    new_stock = request.form.get('stock')
    
    fuel = FuelType.query.get(fuel_id)
    if fuel:
        fuel.price_per_liter = float(new_price)
        fuel.stock_level = float(new_stock)
        db.session.commit()
        flash('Fuel details updated successfully')
    return redirect(url_for('dashboard'))

if __name__ == '__main__':
    with app.app_context():
        db.create_all()
    app.run(debug=True)
