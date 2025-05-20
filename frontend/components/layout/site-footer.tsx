// components/layout/site-footer.tsx
import React from "react";
import Link from "next/link";
import { useTranslations } from "next-intl";

export function SiteFooter() {
  const currentYear = new Date().getFullYear();
  const t = useTranslations("Footer");

  return (
    <footer className="bg-card text-card-foreground py-4 mt-6 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
      <div className="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Desktop footer - more detailed with three-step responsive typography */}
        <div className="hidden md:grid md:grid-cols-2 gap-8">
          <div className="space-y-2">
            <div>
              <h3 className="text-sm md:text-base xl:text-lg font-semibold">
                {t("brand")}
              </h3>
              <p className="text-xs md:text-sm xl:text-base text-muted-foreground mt-1">
                {t("tagline")}
              </p>
            </div>
            <p className="text-xs md:text-xs xl:text-sm text-muted-foreground">
              {t("copyright", { year: currentYear })}
            </p>
          </div>

          <div className="grid grid-cols-2 gap-6">
            <div>
              <h3 className="text-xs md:text-sm xl:text-base font-semibold mb-2">
                {t("resourcesTitle")}
              </h3>
              <ul className="space-y-1">
                <li>
                  <Link
                    href="/about"
                    className="text-xs md:text-xs xl:text-sm text-muted-foreground hover:text-primary transition-colors"
                  >
                    {t("about")}
                  </Link>
                </li>
                <li>
                  <Link
                    href="/faq"
                    className="text-xs md:text-xs xl:text-sm text-muted-foreground hover:text-primary transition-colors"
                  >
                    {t("faq")}
                  </Link>
                </li>
                <li>
                  <Link
                    href="/api"
                    className="text-xs md:text-xs xl:text-sm text-muted-foreground hover:text-primary transition-colors"
                  >
                    {t("api")}
                  </Link>
                </li>
              </ul>
            </div>
            <div>
              <h3 className="text-xs md:text-sm xl:text-base font-semibold mb-2">
                {t("legalTitle")}
              </h3>
              <ul className="space-y-1">
                <li>
                  <Link
                    href="/privacy"
                    className="text-xs md:text-xs xl:text-sm text-muted-foreground hover:text-primary transition-colors"
                  >
                    {t("privacy")}
                  </Link>
                </li>
                <li>
                  <Link
                    href="/terms"
                    className="text-xs md:text-xs xl:text-sm text-muted-foreground hover:text-primary transition-colors"
                  >
                    {t("terms")}
                  </Link>
                </li>
                <li>
                  <Link
                    href="/contact"
                    className="text-xs md:text-xs xl:text-sm text-muted-foreground hover:text-primary transition-colors"
                  >
                    {t("contact")}
                  </Link>
                </li>
              </ul>
            </div>
          </div>
        </div>

        {/* Mobile footer - simplified to avoid bottom nav overlap */}
        <div className="md:hidden flex flex-col items-center text-center pb-16">
          <p className="text-[10px] sm:text-xs text-muted-foreground">
            {t("copyrightShort", { year: currentYear })}
          </p>
          <div className="flex space-x-4 mt-2">
            <Link
              href="/about"
              className="text-[10px] sm:text-xs text-muted-foreground hover:text-primary"
            >
              {t("about")}
            </Link>
            <Link
              href="/privacy"
              className="text-[10px] sm:text-xs text-muted-foreground hover:text-primary"
            >
              {t("privacy")}
            </Link>
            <Link
              href="/terms"
              className="text-[10px] sm:text-xs text-muted-foreground hover:text-primary"
            >
              {t("terms")}
            </Link>
          </div>
        </div>
      </div>
    </footer>
  );
}
