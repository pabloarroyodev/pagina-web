/**
 * Componente base para las calculadoras de dólar
 */
import { MONTHS, populateSelect, validateDates, validatePositiveNumber } from '../utils/common.js';

export class DollarCalculatorComponent {
    /**
     * Constructor
     * @param {DollarService} dollarService - Servicio de datos de dólar
     * @param {string} prefix - Prefijo para los IDs de elementos (vacío para dólar blue, 'Official' para dólar oficial)
     */
    constructor(dollarService, prefix = '') {
        this.dollarService = dollarService;
        this.prefix = prefix;

        // Elementos de la calculadora
        this.pesoAmount = document.getElementById(`pesoAmount${prefix}`);
        this.startYear = document.getElementById(`dollarStartYear${prefix}`);
        this.startMonth = document.getElementById(`dollarStartMonth${prefix}`);
        this.endYear = document.getElementById(`dollarEndYear${prefix}`);
        this.endMonth = document.getElementById(`dollarEndMonth${prefix}`);
        this.startUsdEquivalent = document.getElementById(`startUsdEquivalent${prefix}`);
        this.endUsdEquivalent = document.getElementById(`endUsdEquivalent${prefix}`);
        this.exchangeVariation = document.getElementById(`exchangeVariation${prefix}`);

        // Botón para guardar el cálculo y último cálculo realizado
        this.saveDollarButton = document.getElementById(`saveDollarCalculation${prefix}`);
        this.lastDollarCalculation = null;

        this.init();
    }

    /**
     * Inicializa el componente
     */
    init() {
        this.populateDateSelectors();
        this.setupEventListeners();
        this.calculate(); // Cálculo inicial
    }

    /**
     * Pobla los selectores de fecha
     */
    populateDateSelectors() {
        const years = this.dollarService.getYears();
        
        // Obtener la última fecha disponible
        const lastEntry = this.dollarService.getLatestEntry();
        const lastYear = lastEntry.year;
        const lastMonth = lastEntry.month;

        populateSelect(this.startYear, years.map(year => ({ value: year, text: year })), years[years.length - 1]);
        populateSelect(this.startMonth, MONTHS, 1);
        populateSelect(this.endYear, years.map(year => ({ value: year, text: year })), lastYear);
        populateSelect(this.endMonth, MONTHS, lastMonth);
    }

    /**
     * Configura los event listeners
     */
    setupEventListeners() {
        this.pesoAmount.addEventListener('input', () => this.calculate());
        this.startYear.addEventListener('change', () => this.calculate());
        this.startMonth.addEventListener('change', () => this.calculate());
        this.endYear.addEventListener('change', () => this.calculate());
        this.endMonth.addEventListener('change', () => this.calculate());

        if (this.saveDollarButton) {
            this.saveDollarButton.addEventListener('click', () => this.saveDollarCalculation());
        }
    }

    /**
     * Calcula la evolución del peso respecto al dólar
     */
    calculate() {
        const pesoAmount = parseFloat(this.pesoAmount.value);
        const startYear = parseInt(this.startYear.value);
        const startMonth = parseInt(this.startMonth.value);
        const endYear = parseInt(this.endYear.value);
        const endMonth = parseInt(this.endMonth.value);

        // Limpiar resultados anteriores
        this.clearResults();

        // Verificar si el valor ingresado es válido
        if (!validatePositiveNumber(pesoAmount)) {
            if (pesoAmount !== undefined && !isNaN(pesoAmount)) {
                this.displayError('El valor ingresado debe ser mayor que 0.');
            }
            return;
        }

        // Si algún campo está vacío, simplemente retorna sin hacer cálculos ni mostrar errores
        if (!startYear || !startMonth || !endYear || !endMonth) {
            return;
        }

        const startDate = new Date(startYear, startMonth - 1);
        const endDate = new Date(endYear, endMonth - 1);

        // Validar que la fecha de inicio sea anterior a la fecha final
        if (!validateDates(startDate, endDate)) {
            this.displayError('La fecha de inicio debe ser anterior o igual a la fecha final.');
            return;
        }

        try {
            // Calcular la evolución del peso
            const result = this.dollarService.calculatePesoDollarEvolution(
                pesoAmount, startYear, startMonth, endYear, endMonth
            );

            // Mostrar resultados
            this.startUsdEquivalent.textContent = `$${result.startUsdEquivalent.toFixed(2)}`;
            this.endUsdEquivalent.textContent = `$${result.endUsdEquivalent.toFixed(2)}`;
            this.exchangeVariation.textContent = `${result.percentageChange.toFixed(2)}%`;

            // Agregar clase para colorear según si aumentó o disminuyó
            if (result.percentageChange > 0) {
                this.exchangeVariation.classList.add('positive');
                this.exchangeVariation.classList.remove('negative');
            } else if (result.percentageChange < 0) {
                this.exchangeVariation.classList.add('negative');
                this.exchangeVariation.classList.remove('positive');
            } else {
                this.exchangeVariation.classList.remove('positive', 'negative');
            }

            // Guardar el último cálculo para poder enviarlo al servidor
            this.lastDollarCalculation = {
                pesoAmount,
                startYear,
                startMonth,
                endYear,
                endMonth,
                startUsdEquivalent: result.startUsdEquivalent,
                endUsdEquivalent: result.endUsdEquivalent,
                percentageChange: result.percentageChange,
                dollarType: this.prefix === 'Official' ? 'official' : 'blue'
            };
        } catch (error) {
            this.displayError(error.message);
        }
    }

    /**
     * Envía el último cálculo de dólar al servidor para guardarlo
     */
    async saveDollarCalculation() {
        if (!this.lastDollarCalculation) {
            alert('Primero realizá un cálculo para poder guardarlo.');
            return;
        }

        try {
            const response = await fetch('/pagina_artaza/guardar_dolar.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(this.lastDollarCalculation)
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'No se pudo guardar el cálculo de dólar');
            }

            alert('Cálculo de dólar guardado correctamente.');
        } catch (error) {
            alert(error.message || 'Ocurrió un error al guardar el cálculo de dólar.');
        }
    }

    /**
     * Limpia los resultados en la UI
     */
    clearResults() {
        this.startUsdEquivalent.textContent = '';
        this.endUsdEquivalent.textContent = '';
        this.exchangeVariation.textContent = '';
        this.exchangeVariation.classList.remove('positive', 'negative');
    }

    /**
     * Muestra errores en la UI
     * @param {string} message - Mensaje de error
     */
    displayError(message) {
        this.clearResults();
        this.exchangeVariation.textContent = message || '';
    }
}
