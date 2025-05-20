// components/navigation/mobile-nav.tsx
"use client";

import React, { useEffect } from "react";
import Link from "next/link";
import { XIcon } from "lucide-react";
import { usePathname } from "next/navigation";

interface NavItem {
  label: string;
  href: string;
}

interface MobileNavProps {
  items: NavItem[];
  isOpen: boolean;
  onClose: () => void;
}

export function MobileMenu({ items, isOpen, onClose }: MobileNavProps) {
  const pathname = usePathname();

  // Close the mobile menu when clicking outside
  useEffect(() => {
    const handleOutsideClick = (e: MouseEvent) => {
      // Only close if clicking outside the menu
      const target = e.target as HTMLElement;
      if (!target.closest("[data-mobile-menu]")) {
        onClose();
      }
    };

    if (isOpen) {
      document.addEventListener("click", handleOutsideClick);
    }

    return () => {
      document.removeEventListener("click", handleOutsideClick);
    };
  }, [isOpen, onClose]);

  // Close the menu when navigating to a new page
  useEffect(() => {
    onClose();
  }, [pathname, onClose]);

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-background/80 backdrop-blur-sm z-50 md:hidden">
      <div
        data-mobile-menu
        className="fixed inset-y-0 right-0 w-4/5 max-w-sm bg-card shadow-lg rounded-l-2xl p-6 overflow-y-auto"
        onClick={(e) => e.stopPropagation()}
      >
        <div className="flex justify-end mb-8">
          <button
            onClick={onClose}
            className="rounded-full p-2 bg-muted text-foreground/60 hover:text-foreground"
          >
            <XIcon className="h-5 w-5" />
            <span className="sr-only">Close menu</span>
          </button>
        </div>
        <nav className="flex flex-col space-y-1">
          {items.map((item, index) => {
            const isActive = pathname === item.href;

            return (
              <Link
                key={index}
                href={item.href}
                className={`py-3 px-4 text-sm sm:text-base font-medium rounded-md transition-colors ${
                  isActive
                    ? "bg-primary/10 text-primary"
                    : "text-foreground/80 hover:bg-accent/10"
                }`}
              >
                {item.label}
              </Link>
            );
          })}
        </nav>
      </div>
    </div>
  );
}
