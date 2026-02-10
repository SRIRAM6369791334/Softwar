<link rel="stylesheet" href="/css/map.css">

<div style="display: flex; gap: 20px; height: 80vh;">
    <!-- Sidebar -->
    <div class="map-sidebar card">
        <div class="sidebar-header">
            📦 Unmapped Products
            <input type="text" id="productSearch" placeholder="Search..." style="margin-top: 10px; width: 100%; padding: 5px; background: #0d1117; border: 1px solid var(--border-color); color: #fff;">
        </div>
        <div class="sidebar-content" id="unmappedList">
            <div style="text-align: center; color: #8b949e; padding: 20px;">Type to search...</div>
        </div>
    </div>

    <!-- Map Viewport -->
    <div class="map-viewport" id="mapViewport">
        <div class="map-controls">
            <div class="control-btn" onclick="rotateMap(10)" title="Rotate Right">↻</div>
            <div class="control-btn" onclick="rotateMap(-10)" title="Rotate Left">↺</div>
            <div class="control-btn" onclick="zoomMap(0.1)" title="Zoom In">➕</div>
            <div class="control-btn" onclick="zoomMap(-0.1)" title="Zoom Out">➖</div>
            <div class="control-btn" onclick="resetMap()" title="Reset View">🎯</div>
        </div>

        <div class="iso-plane" id="isoPlane">
            <div class="map-grid" id="mapGrid">
                <!-- Grid Cells Generated via JS -->
            </div>
            <!-- Products Placed via JS -->
        </div>
    </div>
</div>

<script>
let currentRotation = -45;
let currentZoom = 1;
let container = document.getElementById('isoPlane');
let sections = [];
let locations = [];
let gridSize = { width: 12, height: 12 }; // Default

function initMap() {
    loadMapData();
    
    // Search Handler
    document.getElementById('productSearch').addEventListener('input', (e) => {
        if(e.target.value.length > 2) searchProducts(e.target.value);
    });
}

async function loadMapData() {
    const res = await fetch('/map/data');
    const data = await res.json();
    
    sections = data.sections;
    locations = data.locations;
    
    if(sections.length > 0) {
        renderGrid(sections[0].grid_width, sections[0].grid_height);
        gridSize = { width: sections[0].grid_width, height: sections[0].grid_height };
    }
    
    renderLocations();
}

function renderGrid(w, h) {
    const grid = document.getElementById('mapGrid');
    grid.style.gridTemplateColumns = `repeat(${w}, 1fr)`;
    grid.style.gridTemplateRows = `repeat(${h}, 1fr)`;
    grid.innerHTML = '';
    
    for(let y = 0; y < h; y++) {
        for(let x = 0; x < w; x++) {
            const cell = document.createElement('div');
            cell.className = 'grid-cell';
            cell.dataset.x = x;
            cell.dataset.y = y;
            cell.dataset.section = sections[0].id;
            
            // Allow Drop
            cell.addEventListener('dragover', e => e.preventDefault());
            cell.addEventListener('drop', handleDrop);
            
            // Tooltip
            const tooltip = document.createElement('div');
            tooltip.className = 'cell-tooltip';
            tooltip.innerText = `Pos: ${x}, ${y}`;
            cell.appendChild(tooltip);
            
            grid.appendChild(cell);
        }
    }
}

function renderLocations() {
    // Clear existing products on map (but not grid cells)
    document.querySelectorAll('.map-product').forEach(el => el.remove());
    
    locations.forEach(loc => {
        createProductBlock(loc);
    });
}

function createProductBlock(loc) {
    const grid = document.getElementById('mapGrid');
    const cellIndex = (parseInt(loc.y_coord) * gridSize.width) + parseInt(loc.x_coord);
    const cell = grid.children[cellIndex];
    
    if(cell) {
        const block = document.createElement('div');
        block.className = 'map-product';
        block.draggable = true;
        block.innerHTML = `
            <div>
                <strong>${loc.name}</strong><br>
                Price: $${parseFloat(loc.sku).toFixed(2) /* Temp placeholder */}
            </div>
        `;
        block.title = `${loc.name} (Qty: ${loc.stock})`;
        block.dataset.id = loc.product_id;
        
        block.addEventListener('dragstart', handleDragStart);
        
        // Append to CELL, not container, so it moves with grid relative position
        // Actually, CSS says absolute relative to cell is easier
        // But my CSS defines map-product as absolute. 
        // Let's rely on CSS Grid placement if possible?
        // No, isometric absolute positioning is better.
        // Let's modify:
        // Option A: Put block INSIDE cell.
        // This is easiest.
        
        cell.appendChild(block);
        
        // Modify CSS for map-product to be relative to parent if inside cell
        // Or override top/left/width/height defaults
        block.style.top = '10%';
        block.style.left = '10%';
    }
}

async function searchProducts(q) {
    const res = await fetch('/map/search?q=' + q);
    const data = await res.json();
    
    const list = document.getElementById('unmappedList');
    list.innerHTML = data.map(p => `
        <div class="draggable-item" draggable="true" data-id="${p.id}" data-name="${p.name}">
            <span>${p.name}</span>
            <small>${p.sku ?? ''}</small>
        </div>
    `).join('');
    
    // Attach Drag Events
    list.querySelectorAll('.draggable-item').forEach(item => {
        item.addEventListener('dragstart', handleDragStart);
    });
}

// Drag & Drop Logic
let draggedItem = null;

function handleDragStart(e) {
    draggedItem = {
        id: this.dataset.id,
        name: this.dataset.name || this.querySelector('strong').innerText,
        isNew: this.classList.contains('draggable-item')
    };
    e.dataTransfer.setData('text/plain', JSON.stringify(draggedItem));
    this.classList.add('dragging');
}

async function handleDrop(e) {
    e.preventDefault();
    const cell = e.currentTarget; // The grid cell
    const x = cell.dataset.x;
    const y = cell.dataset.y;
    const sectionId = cell.dataset.section;
    
    if(!draggedItem) return;

    // Send Update
    const res = await fetch('/map/update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            product_id: draggedItem.id,
            section_id: sectionId,
            x: x, 
            y: y,
            z: 1
        })
    });
    
    const result = await res.json();
    
    if(result.success) {
        // Refresh Map
        loadMapData();
        // Remove from unmapped list if it was there
        if(draggedItem.isNew) {
            const sidebarItem = document.querySelector(`.draggable-item[data-id="${draggedItem.id}"]`);
            if(sidebarItem) sidebarItem.remove();
        }
    } else {
        alert('Failed to move product');
    }
    
    draggedItem = null;
    document.querySelector('.dragging')?.classList.remove('dragging');
}

// Map Controls
function rotateMap(deg) {
    currentRotation += deg;
    applyTransform();
}

function zoomMap(amount) {
    currentZoom += amount;
    if(currentZoom < 0.5) currentZoom = 0.5;
    if(currentZoom > 2) currentZoom = 2;
    applyTransform();
}

function resetMap() {
    currentRotation = -45;
    currentZoom = 1;
    applyTransform();
}

function applyTransform() {
    container.style.transform = `translate(-50%, -50%) rotateX(60deg) rotateZ(${currentRotation}deg) scale(${currentZoom})`;
    
    // Counter-rotate tooltips to keep them readable?
    // This is hard to do efficiently for all.
    // CSS solution is preferred.
    document.documentElement.style.setProperty('--map-rotation', `${-currentRotation}deg`);
}

// Init
document.addEventListener('DOMContentLoaded', initMap);
</script>
