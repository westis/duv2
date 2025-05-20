import React from "react";
import Link from "next/link";
import { useTranslations } from "next-intl";

export function SiteFooter() {
  const t = useTranslations("Footer");
  const year = new Date().getFullYear();

  return (
    <footer className="w-full border-t border-[var(--color-border)] bg-[var(--color-header)] text-[var(--color-header-foreground)]">
      {/* upper grid – centred & 96 rem wide */}
      <div className="mx-auto w-full max-w-[96rem] px-4 sm:px-6 lg:px-8 py-12 grid gap-8 md:grid-cols-3 text-sm">
        {/* brand / tagline */}
        <div>
          <h3 className="mb-4 font-semibold">{t("brand")}</h3>
          <p className="max-w-xs text-[var(--color-muted-foreground)]">
            {t("tagline")}
          </p>
        </div>

        {/* resources */}
        <div>
          <h4 className="mb-3 font-semibold">{t("resourcesTitle")}</h4>
          <ul className="space-y-2">
            <li>
              <Link href="/about" className="hover:underline">
                {t("about")}
              </Link>
            </li>
            <li>
              <Link href="/faq" className="hover:underline">
                {t("faq")}
              </Link>
            </li>
            <li>
              <Link href="/api" className="hover:underline">
                {t("api")}
              </Link>
            </li>
          </ul>
        </div>

        {/* legal */}
        <div>
          <h4 className="mb-3 font-semibold">{t("legalTitle")}</h4>
          <ul className="space-y-2">
            <li>
              <Link href="/privacy" className="hover:underline">
                {t("privacy")}
              </Link>
            </li>
            <li>
              <Link href="/terms" className="hover:underline">
                {t("terms")}
              </Link>
            </li>
            <li>
              <Link href="/contact" className="hover:underline">
                {t("contact")}
              </Link>
            </li>
          </ul>
        </div>
      </div>

      {/* copyright bar – full width */}
      <div className="bg-[var(--color-surface)] py-4 text-center text-xs text-[var(--color-muted-foreground)]">
        {t("copyright", { year })}
      </div>
    </footer>
  );
}
