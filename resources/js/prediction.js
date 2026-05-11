// Prediksi Page JavaScript

// Prediction history
let predictionHistory = ['Beras Merah', 'Pupuk Organik', 'Kedelai Impor'];

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  setupPredictionHandlers();
  setupRecentSearchHandlers();
  setupAnalysisButtonHandlers();
});

// Setup prediction button handler
function setupPredictionHandlers() {
  const predictBtn = document.querySelector('.btn-predict');
  const productInput = document.getElementById('product-name');

  if (predictBtn && productInput) {
    predictBtn.addEventListener('click', handlePrediction);
    productInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        handlePrediction();
      }
    });
  }
}

// Handle prediction
function handlePrediction() {
  const productInput = document.getElementById('product-name');
  const productName = productInput.value.trim();

  if (!productName) {
    alert('Silakan masukkan nama produk');
    return;
  }

  console.log(`[PREDIKSI] Predicting for: ${productName}`);

  // Add to history
  if (!predictionHistory.includes(productName)) {
    predictionHistory.unshift(productName);
    if (predictionHistory.length > 5) {
      predictionHistory.pop();
    }
  }

  // Show prediction result
  showPredictionResult(productName);

  // Clear input
  productInput.value = '';
}

// Show prediction result
function showPredictionResult(productName) {
  const resultDiv = document.getElementById('predictionResult');
  
  if (!resultDiv) return;

  // Simulate loading
  resultDiv.style.opacity = '0.5';
  resultDiv.style.pointerEvents = 'none';

  setTimeout(() => {
    // Simulate AI analysis data
    const analysisData = {
      name: productName,
      trend: Math.random() > 0.5 ? 'Naik' : 'Turun',
      trendPercent: Math.floor(Math.random() * 30) + 5,
      recommendedQty: Math.floor(Math.random() * 500) + 100,
      reorderDays: Math.floor(Math.random() * 21) + 7,
      nextRestock: new Date(Date.now() + Math.random() * 30 * 24 * 60 * 60 * 1000).toLocaleDateString('id-ID')
    };

    // Create result HTML
    const resultHTML = `
      <div style="width: 100%; max-width: 800px; text-align: left; animation: fadeInUp 0.5s ease-out;">
        <div style="margin-bottom: 32px;">
          <h4 style="font-size: 20px; font-weight: 700; color: #1f201b; margin-bottom: 16px;">
            ${productName}
          </h4>
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <!-- Trend Card -->
            <div style="background-color: #faf8fc; border-left: 4px solid #f5a623; padding: 16px; border-radius: 8px;">
              <p style="font-size: 12px; font-weight: 700; color: #80827c; text-transform: uppercase; margin-bottom: 8px;">
                <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle; margin-right: 8px;">trending_up</span>
                Tren Permintaan
              </p>
              <p style="font-size: 24px; font-weight: 800; color: #f5a623; margin-bottom: 4px;">
                ${analysisData.trend} ${analysisData.trendPercent}%
              </p>
              <p style="font-size: 12px; color: #4a4844;">Minggu ini dibanding minggu lalu</p>
            </div>

            <!-- Quantity Card -->
            <div style="background-color: #faf8fc; border-left: 4px solid #004d40; padding: 16px; border-radius: 8px;">
              <p style="font-size: 12px; font-weight: 700; color: #80827c; text-transform: uppercase; margin-bottom: 8px;">
                <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle; margin-right: 8px;">inventory</span>
                Qty Optimal
              </p>
              <p style="font-size: 24px; font-weight: 800; color: #004d40; margin-bottom: 4px;">
                ${analysisData.recommendedQty} Unit
              </p>
              <p style="font-size: 12px; color: #4a4844;">Untuk menghindari stockout</p>
            </div>

            <!-- Reorder Card -->
            <div style="background-color: #faf8fc; border-left: 4px solid #1f201b; padding: 16px; border-radius: 8px;">
              <p style="font-size: 12px; font-weight: 700; color: #80827c; text-transform: uppercase; margin-bottom: 8px;">
                <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle; margin-right: 8px;">event_repeat</span>
                Re-order Dalam
              </p>
              <p style="font-size: 24px; font-weight: 800; color: #1f201b; margin-bottom: 4px;">
                ${analysisData.reorderDays} Hari
              </p>
              <p style="font-size: 12px; color: #4a4844;">Tanggal: ${analysisData.nextRestock}</p>
            </div>
          </div>
        </div>

        <!-- Additional Insights -->
        <div style="background-color: #e8f5e9; padding: 16px; border-radius: 8px; margin-top: 16px;">
          <p style="font-weight: 700; color: #1b5e20; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
            <span class="material-symbols-outlined">insights</span>
            Insights AI
          </p>
          <p style="font-size: 14px; color: #1b5e20; line-height: 1.6;">
            Berdasarkan analisis data historis, curah hujan, dan tren pasar, kami merekomendasikan untuk melakukan 
            re-order sebanyak <strong>${analysisData.recommendedQty} unit</strong> dalam ${analysisData.reorderDays} hari 
            mendatang untuk mengoptimalkan stok ${productName}.
          </p>
        </div>
      </div>
    `;

    resultDiv.innerHTML = resultHTML;
    resultDiv.style.opacity = '1';
    resultDiv.style.pointerEvents = 'auto';

    // Scroll to result
    setTimeout(() => {
      resultDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 100);
  }, 1500);
}

// Setup recent search handlers
function setupRecentSearchHandlers() {
  const recentSearches = document.querySelectorAll('.recent-search');
  
  recentSearches.forEach(search => {
    search.addEventListener('click', () => {
      const productName = search.textContent.trim();
      const productInput = document.getElementById('product-name');
      
      if (productInput) {
        productInput.value = productName;
        handlePrediction();
      }
    });
  });
}

// Setup analysis button handlers
function setupAnalysisButtonHandlers() {
  const analysisButtons = document.querySelectorAll('.btn-analyze');
  
  analysisButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const card = btn.closest('.seasonal-card');
      const productName = card.querySelector('.seasonal-card-name').textContent.trim();
      const productInput = document.getElementById('product-name');
      
      if (productInput) {
        productInput.value = productName;
        handlePrediction();
      }
    });
  });
}
