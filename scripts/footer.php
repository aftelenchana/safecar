<?php
// footer.php - Footer reusable para GUIBIS
?>
<footer class="footer">
  <div class="footer-content">
    <div class="footer-grid">
      <div class="footer-section">
        <h5>GUIBIS</h5>
        <p>APIs confiables para verificación de datos. Consultas de registro civil, RUC, antecedentes y más con la máxima seguridad y precisión.</p>
        <div class="social-links">
          <a href="<?php echo !empty($facebook) ? $facebook : '#'; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
          <a href="<?php echo !empty($twitter) ? $twitter : '#'; ?>" target="_blank"><i class="fab fa-twitter"></i></a>
          <a href="<?php echo !empty($linkedin) ? $linkedin : '#'; ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a>
          <a href="<?php echo !empty($instagram) ? $instagram : '#'; ?>" target="_blank"><i class="fab fa-instagram"></i></a>
        </div>
      </div>

      <div class="footer-section">
        <h5>Enlaces Rápidos</h5>
        <ul class="footer-links">
          <li><a href="/"><i class="fas fa-chevron-right"></i> Inicio</a></li>
          <li><a href="/#servicios"><i class="fas fa-chevron-right"></i> Servicios</a></li>
          <li><a href="/#precios"><i class="fas fa-chevron-right"></i> Precios</a></li>
          <li><a href="/#contacto"><i class="fas fa-chevron-right"></i> Contacto</a></li>
        </ul>
      </div>

      <div class="footer-section">
        <h5>Legal</h5>
        <ul class="footer-links">
          <li><a href="/terminos"><i class="fas fa-chevron-right"></i> Términos de Servicio</a></li>
          <li><a href="/privacidad"><i class="fas fa-chevron-right"></i> Política de Privacidad</a></li>
          <li><a href="/cookies"><i class="fas fa-chevron-right"></i> Cookies</a></li>
          <li><a href="/licencias"><i class="fas fa-chevron-right"></i> Licencias</a></li>
        </ul>
      </div>

      <div class="footer-section">
        <h5>Contacto</h5>
        <ul class="footer-links">
          <li><a href="mailto:<?php echo !empty($email) ? $email : 'info@guibis.com'; ?>"><i class="fas fa-envelope"></i> <?php echo !empty($email) ? $email : 'info@guibis.com'; ?></a></li>
          <li><a href="tel:<?php echo !empty($celular) ? $celular : '+1 (555) 123-4567'; ?>"><i class="fas fa-phone"></i> <?php echo !empty($celular) ? $celular : '+1 (555) 123-4567'; ?></a></li>
          <li><a href="#"><i class="fas fa-map-marker-alt"></i> Ciudad, País</a></li>
          <li><a href="#"><i class="fas fa-clock"></i> Lun - Vie: 9:00 - 18:00</a></li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <p>&copy; <?php echo date('Y'); ?> GUIBIS. Todos los derechos reservados. | Desarrollado con <i class="fas fa-heart" style="color: #00bcd4;"></i> para tu seguridad</p>
    </div>
  </div>
</footer>
