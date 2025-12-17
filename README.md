# PHP_Laravel12_Repeater_Implement_Using_Vue.JS

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel">
  <img src="https://img.shields.io/badge/Vue.js-3.x-42b883?style=for-the-badge&logo=vue.js">
  <img src="https://img.shields.io/badge/Inertia.js-SPA-blueviolet?style=for-the-badge">
  <img src="https://img.shields.io/badge/Breeze-Auth-success?style=for-the-badge">
</p>

---

##  Overview

This project demonstrates a **Repeater CRUD module** using  
**Laravel 12 + Breeze + Inertia.js + Vue 3**.

### Concept
- **Product** → Master
- **Product Images** → Repeater (Child)
- Add / Remove dynamic rows
- Multiple image upload

---

##  Features

- Laravel 12 backend
- Breeze authentication
- Vue 3 + Inertia.js SPA
- Dynamic repeater rows
- Multiple image upload
- Image preview
- Remove existing images
- Clean Tailwind UI

---

##  Folder Structure

```
app/
├── Http/Controllers/
│   └── ProductController.php
├── Models/
│   ├── Product.php
│   └── ProductImage.php

database/
└── migrations/
    ├── xxxx_create_products_table.php
    └── xxxx_create_product_images_table.php

resources/
├── js/
│   ├── app.js
│   └── Pages/
│       └── Product/
│           ├── Index.vue
│           ├── Create.vue
│           └── Edit.vue
└── views/
    └── app.blade.php

public/
└── products/

routes/web.php
.env
README.md
```

---

##  STEP 1: Install Laravel 12

```bash
composer create-project laravel/laravel repeater-crud
```

---

##  STEP 2: Database Configuration (.env)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=repeater
DB_USERNAME=root
DB_PASSWORD=
```

Create database **repeater** in phpMyAdmin.

---

##  STEP 3: Install Breeze (Vue + Inertia)

```bash
composer require laravel/breeze --dev

php artisan breeze:install vue

npm install

php artisan migrate

npm run dev

php artisan serve

```

---

##  STEP 4: Create Models & Migrations

```bash
php artisan make:model Product -m

php artisan make:model ProductImage -m
```

---

##  STEP 5: Migrations

### create_products_table.php

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('details');
    $table->decimal('price',10,2);
    $table->timestamps();
});
```

### create_product_images_table.php

```php
Schema::create('product_images', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->cascadeOnDelete();
    $table->string('image');
    $table->timestamps();
});
```

Run:

```bash
php artisan migrate
```

---

##  STEP 6: Create Image Folder

```bash
mkdir public/products
```

---

##  STEP 7: Models

### Product.php

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name','details','price'];

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
}
```

### ProductImage.php

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = ['product_id','image'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
```

---

##  STEP 8: Controller

```bash
php artisan make:controller ProductController
```

```php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        return Inertia::render('Product/Index', [
            'products' => Product::with('images')->latest()->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Product/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'details' => 'required',
            'price' => 'required|numeric',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $product = Product::create($request->only('name','details','price'));

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $name = time().rand(1,99).'.'.$img->extension();
                $img->move(public_path('products'), $name);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $name
                ]);
            }
        }

        return redirect()->route('product.index');
    }

    public function edit(Product $product)
    {
        return Inertia::render('Product/Edit', [
            'product' => $product->load('images')
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $product->update($request->only('name','details','price'));

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $name = time().rand(1,99).'.'.$img->extension();
                $img->move(public_path('products'), $name);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $name
                ]);
            }
        }

        return redirect()->route('product.index');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $img) {
            @unlink(public_path('products/'.$img->image));
            $img->delete();
        }

        $product->delete();
        return back();
    }
}
```

---

##  STEP 9: Routes

```php
use App\Http\Controllers\ProductController;

Route::middleware(['auth'])->group(function () {
    Route::resource('product', ProductController::class);
});
```

---

##  STEP 10: Inertia Setup

### resources/js/app.js

```js
import '../css/app.css'
import './bootstrap'

import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createApp, h } from 'vue'

createInertiaApp({
  resolve: name =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue')
    ),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el)
  },
})
```

### resources/views/app.blade.php

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel Repeater</title>
    @vite(['resources/js/app.js'])
</head>
<body>
    @inertia
</body>
</html>
```

---

##  STEP 11: Vue Pages 

### resources/js/Pages/Product/Index.vue 

```vue
<script setup>
import { Link } from '@inertiajs/vue3'
defineProps({ products:Array })
</script>

<template>
  <div>
    <h1>Products</h1>
    <Link href="/product/create">Add Product</Link>

    <div v-for="p in products" :key="p.id">
      <h3>{{ p.name }}</h3>
      <img v-for="img in p.images" :src="`/products/${img.image}`" width="80">
      <Link :href="`/product/${p.id}/edit`">Edit</Link>
    </div>
  </div>
</template>
```

### resources/js/Pages/Product/Create.vue

```vue
<script setup>
import { useForm, Link } from '@inertiajs/vue3'

const form = useForm({
  name:'',
  details:'',
  price:'',
  images:[]
})

const submit = () => {
  form.post('/product', { forceFormData:true })
}
</script>

<template>
  <form @submit.prevent="submit">
    <input v-model="form.name" placeholder="Name">
    <textarea v-model="form.details"></textarea>
    <input v-model="form.price" type="number">

    <input type="file" multiple @change="e => form.images = e.target.files">

    <button>Save</button>
    <Link href="/product">Back</Link>
  </form>
</template>
```

### resources/js/Pages/Product/Edit.vue 

```vue
<script setup>
import { useForm, Link } from '@inertiajs/vue3'
const props = defineProps({ product:Object })

const form = useForm({
  name: props.product.name,
  details: props.product.details,
  price: props.product.price,
  images:[]
})

const update = () => {
  form.post(`/product/${props.product.id}`, {
    _method:'put',
    forceFormData:true
  })
}
</script>

<template>
  <form @submit.prevent="update">
    <input v-model="form.name">
    <textarea v-model="form.details"></textarea>
    <input type="number" v-model="form.price">

    <div>
      <img v-for="img in product.images" :src="`/products/${img.image}`" width="80">
    </div>

    <input type="file" multiple @change="e => form.images = e.target.files">

    <button>Update</button>
    <Link href="/product">Back</Link>
  </form>
</template>
```

---

##  Final URL

```
http://127.0.0.1:8000/product
```
INDEX PAGE:-

<img width="1278" height="332" alt="Screenshot 2025-12-17 153644" src="https://github.com/user-attachments/assets/7fdbe799-60af-4ecd-b6ab-ab63cb47db6e" />


CREATE PRODUCT PAGE:-

<img width="696" height="655" alt="Screenshot 2025-12-17 153633" src="https://github.com/user-attachments/assets/e5decaa0-a269-491f-a904-ba205f84881e" />


EDIT PRODUCT PAGE;-

<img width="734" height="713" alt="Screenshot 2025-12-17 153853" src="https://github.com/user-attachments/assets/9ba7e26a-1590-433d-8cb2-b27f28b79ade" />


---


